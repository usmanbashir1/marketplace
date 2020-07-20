<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Controller\Account;

/**
 * Class LoginPost
 * @package WeltPixel\SocialLogin\Controller\Account
 */
class LoginPost extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $checkoutParam = $this->getRequest()->getParam('sociallogin-checkout');
        $redirectUrl = $this->getRequest()->getParam(\Magento\Customer\Model\Url::REFERER_QUERY_PARAM_NAME);
        if ($redirectUrl && !$checkoutParam) {
            $redirectUrl = base64_decode($redirectUrl);
            $this->getResponse()->setRedirect($redirectUrl);
        } elseif($checkoutParam){
            $this->getResponse()->setRedirect($checkoutParam);
        } else {
            $this->_redirect('/');
        }
    }

}