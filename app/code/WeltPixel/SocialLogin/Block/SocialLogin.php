<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Block;

/**
 * Class SocialLogin
 * @package WeltPixel\SocialLogin\Block
 */
class SocialLogin extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \WeltPixel\SocialLogin\Helper\Data
     */
    protected $slHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var array
     */
    protected $_socialMedia = [
        'fb' => 'https://www.facebook.com/dialog/oauth/?',
        'amazon' => 'https://www.amazon.com/ap/oa/?',
        'google' => 'https://accounts.google.com/o/oauth2/v2/auth?scope=email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+openid&access_type=offline&include_granted_scopes=true&state=state_parameter_passthrough_value&'
    ];


    protected $formKey;

    /**
     * SocialLogin constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \WeltPixel\SocialLogin\Helper\Data $slHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \WeltPixel\SocialLogin\Helper\Data $slHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->slHelper = $slHelper;
        $this->_request = $request;
        $this->formKey = $formKey;
        if ($this->slHelper->isEnabled()) {
            $this->pageConfig->addBodyClass('wp-sl');
        }
    }

    /**
     * @return string|void
     */
    protected function _toHtml()
    {
            if (!$this->slHelper->isEnabled()) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getSkipModules()
    {
        $skip = $this->slHelper->getSkipModulesReferer();
        return json_encode($skip);
    }

    /**
     * @return bool|string
     */
    protected function _isCheckoutPage() {
        $route      = $this->_request->getRouteName();
        $controller = $this->_request->getControllerName();
        if($route == 'checkout' && $controller == 'index') {
            return $this->getUrl('checkout/index/index', ['secure' => true]);
        } else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    protected function _isCartPage() {
        $route      = $this->_request->getRouteName();
        $controller = $this->_request->getControllerName();
        if($controller == 'cart' && $route == 'checkout') {
            return $this->getUrl('checkout/cart/index', ['secure' => true]);
        } else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    public function getCurrentPageRedirectUrl() {
        $url = false;
        if($cartUrl = $this->_isCartPage()) {
            return $cartUrl;
        } elseif($checkoutUrl = $this->_isCheckoutPage()) {
            return $checkoutUrl;
        } else {
            return $url;
        }
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn() {
        return $this->slHelper->isCustomerLoggedIn();
    }

    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }


}