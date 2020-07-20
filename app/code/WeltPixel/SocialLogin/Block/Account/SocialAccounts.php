<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Block\Account;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use \Magento\Framework\App\Response\Http;
use WeltPixel\SocialLogin\Model\SocialloginFactory;

/**
 * Class SocialAccounts
 * @package WeltPixel\SocialLogin\Block\Account
 */
class SocialAccounts extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session 
     */
    protected $customerSession;
    /**
     * @var SocialloginFactory
     */
    protected $socialloginFactory;
    /**
     * @var Http
     */
    protected $response;

    public function __construct(
        Template\Context $context,
        Session $customerSession,
        SocialloginFactory $socialloginFactory,
        Http $response,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->socialloginFactory = $socialloginFactory;
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function getSocialAccounts()
    {
        $customer = $this->getCustomer();
        if(!$customer->getId()) {
            $this->response->setRedirect('customer/account/');
        }
        $socialAccountsCollection = $this->socialloginFactory->create()->getUsersByCustomerId($customer->getId());
        if($socialAccountsCollection->getSize() < 1) {
            return false;
        }

        return $socialAccountsCollection;
    }

    /**
     * @return mixed
     */
    public function getCustomer() {

        return $this->customerSession->getCustomer();
    }

    /**
     * @param $userId
     * @return string
     */
    public function getUnlinkUrl($userId) {
        return $this->getUrl('sociallogin/account/unlink/', ['id' => $userId]);
    }
}