<?php

namespace Cminds\Supplierfrontendproductuploader\Block\Settings;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Oauth\Helper\Oauth as OauthHelper;
use Cminds\Supplierfrontendproductuploader\Model\ResourceModel\ApiToken\CollectionFactory as ApiTokenCollectionFactory;
use Cminds\Supplierfrontendproductuploader\Model\ApiTokenFactory;
use Cminds\Supplierfrontendproductuploader\Api\Data\TokenInterface;

/**
 * Cminds Supplierfrontendproductuploader notifications settings block.
 *
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 */
class Token extends Template
{
    /**
     * Customer session object.
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Oauth Helper object.
     *
     * @var OauthHelper
     */
    protected $oauthHelper;

    /**
     * Token factory.
     *
     * @var ApiTokenFactory
     */
    protected $apiTokenFactory;

    /**
     * Token collection factory.
     *
     * @var ApiTokenCollectionFactory
     */
    protected $apiTokenCollectionFactory;

    /**
     * Object constructor.
     *
     * @param Context                       $context         Context object.
     * @param CustomerSession               $customerSession Customer session object.
     * @param OauthHelper                   $oauthHelper
     * @param ApiTokenCollectionFactory     $apiTokenCollectionFactory
     * @param ApiTokenFactory               $apiTokenFactory
     * @param array                         $data            Data array.
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        OauthHelper $oauthHelper,
        ApiTokenCollectionFactory $apiTokenCollectionFactory,
        ApiTokenFactory $apiTokenFactory,
        array $data
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
        $this->oauthHelper = $oauthHelper;
        $this->apiTokenCollectionFactory = $apiTokenCollectionFactory;
        $this->apiTokenFactory = $apiTokenFactory;
    }

    /**
     * Retrieve currently logged in customer object.
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * Return bool value if supplier can configure ordered products
     * notification.
     *
     * @return bool
     */
    public function getApiToken()
    {
        $customer = $this->getCustomer();
        $tokenCollection = $this->apiTokenCollectionFactory->create();
        $tokenCollection->addFieldToFilter(TokenInterface::CUSTOMER_ID, $customer->getId());
        $token = null;

        if(0 === $tokenCollection->count()){
            $tokenModel = $this->apiTokenFactory->create();
            $token = $this->oauthHelper->generateToken();
            $tokenModel->setCustomerToken($token)
                ->setCustomerId($customer->getId())
                ->save();
        } else {
            $token = $tokenCollection->getFirstItem()->getCustomerKey();
        }

        return $token;
    }
}
