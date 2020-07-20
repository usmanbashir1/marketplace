<?php

namespace Cminds\MarketplaceRma\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Email
 *
 * @package Cminds\MarketplaceRma\Helper
 */
class Email extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Context
     */
    private $context;

    /**
     * Email constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder      $transportBuilder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder
    ) {
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->context = $context;
        $this->scopeConfig = $this->context->getScopeConfig();

        parent::__construct($context);
    }

    /**
     * Send email.
     *
     * @param $data
     */
    public function sendMail($data)
    {
        $storeContactEmail = $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            ScopeInterface::SCOPE_STORE
        );

        $storeContactName = $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            ScopeInterface::SCOPE_STORE
        );

        $templateOptions = [
                                'area' => Area::AREA_FRONTEND,
                                'store' => $this->storeManager->getStore()->getId()
                            ];
        $templateVars = [
                            'store' => $this->storeManager->getStore(),
                            'receiver_name' => $data['receiver_name'],
                            'subject'   => $data['subject'],
                            'message'   => $data['message']
                        ];
        $from = [
                    'email' => $storeContactEmail,
                    'name' =>  $storeContactName
                ];

        $to = [$data['receiver_email']];
        $transport = $this->transportBuilder->setTemplateIdentifier('rma_status_template')
                        ->setTemplateOptions($templateOptions)
                        ->setTemplateVars($templateVars)
                        ->setFrom($from)
                        ->addTo($to)
                        ->getTransport();
        try {
            $transport->sendMessage();
        } catch (MailException $e) {
            return;
        }
    }
}
