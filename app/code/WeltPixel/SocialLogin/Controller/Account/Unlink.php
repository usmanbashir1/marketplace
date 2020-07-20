<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 * @author      WeltPixel TEAM
 */

namespace WeltPixel\SocialLogin\Controller\Account;

use WeltPixel\SocialLogin\Model\SocialloginFactory;

/**
 * Class Socialaccounts
 * @package WeltPixel\SocialLogin\Controller\Account
 */
class Unlink extends \WeltPixel\SocialLogin\Controller\AbstractAccount
{

    /**
     * @var SocialloginFactory
     */
    protected $socialloginFactory;

    /**
     * Unlink constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \WeltPixel\SocialLogin\Helper\Data $slHelper
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\Layout\Interceptor $layout
     * @param SocialloginFactory $socialloginFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \WeltPixel\SocialLogin\Helper\Data $slHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Layout\Interceptor $layout,
        SocialloginFactory $socialloginFactory
    )
    {
        parent::__construct($context, $customerSession, $slHelper, $storeManager, $resultRawFactory, $layout);
        $this->socialloginFactory = $socialloginFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if($id) {
            $unlinkResponse = $this->socialloginFactory->create()->unlinkUser($id);
            if($unlinkResponse) {
                $this->messageManager->addSuccess(__('Account unlinked successfully.'));
            } else {
                $this->messageManager->addError(__('No link id provided.'));
            }
        } else {
            $this->messageManager->addError(__('An error occurred, please try again.'));
        }

        $redirectUrl = $this->_getUrl('sociallogin/account/socialaccounts');
        $this->getResponse()->setRedirect($redirectUrl);
    }
}