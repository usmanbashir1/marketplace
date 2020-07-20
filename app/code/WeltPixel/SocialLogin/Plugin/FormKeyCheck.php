<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Plugin;

use Magento\Framework\Data\Form\FormKey;

/**
 * Class FormKeyCheck
 * @package WeltPixel\SocialLogin\Plugin
 */
class FormKeyCheck
{
    protected $customerSession;

    /**
     * ResultPage constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(\Magento\Customer\Model\Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * @param FormKey $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetFormKey(FormKey $subject, callable $proceed)
    {
        $result = $proceed();
        $slSession = $this->customerSession->getSociallogin();
        if(is_array($slSession) && !empty($slSession)) {
            if(isset($slSession['sl_form_key'])) {
                $result = $slSession['sl_form_key'];
                unset($slSession['sl_form_key']);
                $this->customerSession->setData('sociallogin', $slSession);
            }
        }

        return $result;
    }
}