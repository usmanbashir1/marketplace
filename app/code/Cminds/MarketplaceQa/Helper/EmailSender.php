<?php

namespace Cminds\MarketplaceQa\Helper;

use Cminds\MarketplaceQa\Helper\Data as MarketplaceQaHelper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\CustomerFactory as SupplierFactory;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class EmailSender
{
    const ANSWER_CUSTOMER_TEMPLATE = 'answer_customer_notification_template';
    const QUESTION_ADMIN_TEMPLATE = 'question_admin_notification_template';
    const QUESTION_CUSTOMER_TEMPLATE = 'question_customer_notification_template';
    const QUESTION_SUPPLIER_TEMPLATE = 'question_supplier_notification_template';

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SupplierFactory
     */
    private $supplierFactory;

    /**
     * @var Data
     */
    private $marketplaceQaHelper;

    /**
     * EmailSender constructor.
     *
     * @param ProductFactory        $productFactory
     * @param TransportBuilder      $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param SupplierFactory       $supplierFactory
     * @param MarketplaceQaHelper   $marketplaceQaHelper
     */
    public function __construct(
        ProductFactory $productFactory,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        SupplierFactory $supplierFactory,
        MarketplaceQaHelper $marketplaceQaHelper
    ) {
        $this->productFactory = $productFactory;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->supplierFactory = $supplierFactory;
        $this->marketplaceQaHelper = $marketplaceQaHelper;
    }

    /**
     * @param $question
     * @param $model
     * @param bool $toSupplier
     * @param bool $toAdmin
     * @param null $answer
     */
    public function prepareEmail($question, $model, $toSupplier = false, $toAdmin = false, $answer = null)
    {
        $store = $this->storeManager->getDefaultStoreView()->getId();
        $product = $this->productFactory->create()->load($model->getData('product_id'));

        if ($toSupplier) {
            $supplierId = $product->getData('creator_id');

            if ($supplierId === null || $supplierId === '' || $supplierId == 0) {
                return;
            }

            $supplier = $this->supplierFactory->create()->load($supplierId);
            $supplierEmail = $supplier->getEmail();
            $supplierName = $supplier->getName();

            $templateVars = [
                'supplier_name' => $supplierName,
                'product_name' => $product->getName(),
                'product_url' => $product->setStoreId($store)->getUrlInStore(),
                'question' => $question
            ];

            $this->sendEmail($supplierEmail, $templateVars, $store, self::QUESTION_SUPPLIER_TEMPLATE);
        } elseif($toAdmin === false && $answer === null) {
            $customerEmail = $model->getData('customer_email');
            $customerName = $model->getData('customer_name');

            $templateVars = [
                'customer_name' => $customerName,
                'product_name' => $product->getName(),
                'product_url' => $product->setStoreId($store)->getUrlInStore(),
                'question' => $question
            ];

            $this->sendEmail($customerEmail, $templateVars, $store, self::QUESTION_CUSTOMER_TEMPLATE);
        }

        if ($toAdmin) {
            $adminEmail = $this->marketplaceQaHelper->getStoreGeneralEmail();
            $adminName = $this->marketplaceQaHelper->getStoreGeneralName();

            $templateVars = [
                'admin_name' => $adminName,
                'product_name' => $product->getName(),
                'product_url' => $product->setStoreId($store)->getUrlInStore(),
                'question' => $question
            ];

            $this->sendEmail($adminEmail, $templateVars, $store, self::QUESTION_ADMIN_TEMPLATE);
        }

        if ($answer !== null) {
            $customerEmail = $model->getOrigData('customer_email');
            $productId = $model->getOrigData('product_id');
            $product = $this->productFactory->create()->load($productId);
            $customerName = $model->getOrigData('customer_name');

            $templateVars = [
                'customer_name' => $customerName,
                'product_name' => $product->getName(),
                'product_url' => $product->setStoreId($store)->getUrlInStore(),
                'question' => $question,
                'answer' => $answer
            ];

            $this->sendEmail($customerEmail, $templateVars, $store, self::ANSWER_CUSTOMER_TEMPLATE);
        }
    }

    /**
     * @param $recipient
     * @param $templateVars
     * @param $store
     * @param $emailTemplate
     */
    private function sendEmail($recipient, $templateVars, $store, $emailTemplate)
    {
        $transport = $this->transportBuilder->setTemplateIdentifier($emailTemplate)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars($templateVars)
            ->setFrom('general')
            ->addTo($recipient)
            ->getTransport();

        try {
            $transport->sendMessage();
        } catch (MailException $e) {
        }
    }
}
