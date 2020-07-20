<?php
/**
 * Cminds
 */
namespace Cminds\Marketplace\Model\Supplier;

use Magento\Customer\Model\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

class Notification
{
    const TEMPLATE_ID = "custom_supplier_account_approval";

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * @var Url
     */
    private $urlBuilder;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * Notification constructor.
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param Url $urlBuilder
     * @param StateInterface $state
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfigInterface,
        Url $urlBuilder,
        StateInterface $state,
        TransportBuilder $transportBuilder
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->urlBuilder = $urlBuilder;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param \Magento\Customer\Model\Data\Customer $customerData
     */
    public function sendEmail(\Magento\Customer\Model\Data\Customer $customerData)
    {
        $storeEmailAddress = $this->scopeConfigInterface->getValue(
            'trans_email/ident_' . 'general' . '/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $fromName = $this->scopeConfigInterface->getValue(
            'trans_email/ident_' . 'general' . '/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->inlineTranslation->suspend();

        $from = ['email' => $storeEmailAddress, 'name' => $fromName];

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $customerData->getStoreId()
        ];

        try {
            $templateVars = [
                'supplier_name' => $customerData->getFirstname() . ' ' . $customerData->getLastname(),
                'account_link' => str_replace('admin/', '', $this->urlBuilder->getLoginUrl())
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier(self::TEMPLATE_ID, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($customerData->getEmail())
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return $this;
        } catch (\Exception $exception) {
            return  $exception;
        }
    }
}
