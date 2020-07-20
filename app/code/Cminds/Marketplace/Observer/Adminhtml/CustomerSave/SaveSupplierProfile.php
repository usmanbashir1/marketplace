<?php

namespace Cminds\Marketplace\Observer\Adminhtml\CustomerSave;

use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Cminds\Marketplace\Model\Fields;
use Cminds\Marketplace\Model\Supplier\Notification;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class SaveSupplierProfile extends CustomerSaveAbstract
{
    const NOTIFY_SUPPLIER_CONFIG_PATH = "configuration_marketplace/configure/notify_supplier_when_his_profile_was_approved";

    protected $marketplaceHelper;
    protected $customerFactory;
    protected $fields;
    protected $logger;
    protected $scopeConfigInterface;

    protected $notification;

    public function __construct(
        MarketplaceHelper $marketplaceHelper,
        CustomerFactory $customerFactory,
        Fields $fields,
        Context $context,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfigInterface,
        Notification $notification
    ) {
        parent::__construct(
            $marketplaceHelper,
            $context
        );

        $this->customerFactory = $customerFactory;
        $this->fields = $fields;
        $this->logger = $logger;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->notification = $notification;
    }

    public function execute(Observer $observer)
    {
        try {
            $notifySupplier = false;

            $postData = $this->request->getPostValue();

            $customerData = $observer->getCustomer();

            $customerModel = $this->customerFactory->create();

            if (isset($postData['action'])
                && $postData['action'] == 'save_remark'
            ) {
                $customerModel->setCustomAttribute('supplier_remark', $postData['remark']);
                $customerModel->setCustomAttribute('rejected_notfication_seen', 0);
            }

            if (isset($postData['action'])
                && $postData['action'] == 'approve'
            ) {
                $notifySupplier = true;
                $customerData->setCustomAttribute('supplier_profile_approved', 1);
                $customerData->setCustomAttribute('supplier_profile_visible', 1);
            }

            if (isset($postData['action'])
                && $postData['action'] == 'disapprove'
            ) {
                $customerData->setCustomAttribute('supplier_profile_approved', 0);
                $customerData->setCustomAttribute('rejected_notfication_seen', 1);
            }

            if (isset($postData['action'])
                && $postData['action'] == 'approve_changes'
            ) {
                $newCustomFieldsValues = $this
                    ->_prepareCustomFieldsValues($postData);

                $customerData->setCustomAttribute('supplier_remark', null);
                $customerData->setCustomAttribute('supplier_name_new', '');
                $customerData->setCustomAttribute('supplier_description_new', '');
                $customerData->setCustomAttribute(
                    'custom_fields_values',
                    serialize($newCustomFieldsValues)
                );
                $customerData->setCustomAttribute('new_custom_fields_values', null);
                $customerData->setCustomAttribute(
                    'supplier_name',
                    htmlentities(
                        $postData['supplier_name_new'],
                        ENT_QUOTES,
                        "UTF-8"
                    )
                );
                $customerData->setCustomAttribute(
                    'supplier_description',
                    $postData['supplier_description_new']
                );
                $customerData->setCustomAttribute('supplier_profile_visible', 1);
                $customerData->setCustomAttribute('supplier_profile_approved', 1);
                $customerData->setCustomAttribute('rejected_notfication_seen', 1);
            }
            $customerModel->updateData($customerData);
            $customerModel->save();

            if ($notifySupplier && $this->scopeConfigInterface->getValue(self::NOTIFY_SUPPLIER_CONFIG_PATH)) {
                $this->notification->sendEmail($customerData);
            }
        } catch (LocalizedException $e) {
            $this->logger->info($e->getMessage());
        }
    }

    private function _prepareCustomFieldsValues($postData)
    {
        $customFieldsCollection = $this->fields->getCollection();

        $customFieldsValues = [];

        foreach ($customFieldsCollection as $field) {
            if (isset($postData[$field->getName() . '_new'])) {
                if ($field->getIsRequired()
                    && $postData[$field->getName() . '_new'] == ''
                ) {
                    throw new LocalizedException(
                        __(
                            "Field " . $field->getName() . " is required"
                        )
                    );
                }

                if ($field->getType() == 'date'
                    && !strtotime($postData[$field->getName() . '_new'])
                ) {
                    throw new LocalizedException(
                        __(
                            "Field " . $field->getName() . " is not valid date"
                        )
                    );
                }

                $customFieldsValues[] = [
                    'name' => $field->getName(),
                    'value' => $postData[$field->getName() . '_new'],
                ];
            }
        }

        return $customFieldsValues;
    }
}
