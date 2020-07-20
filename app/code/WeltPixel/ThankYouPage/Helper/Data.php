<?php
namespace WeltPixel\ThankYouPage\Helper;

/**
 * Class Data
 * @package WeltPixel\ThankYouPage\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_thankYouPageOptions;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_thankYouPageOptions = $this->scopeConfig->getValue('weltpixel_thankyoupage', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return boolean
     */
    public function isOrderDetailsEnabled()
    {
        return $this->_thankYouPageOptions['order_details']['enable'];
    }

    /**
     * @return string
     */
    public function showContinueShopping()
    {
        return $this->_thankYouPageOptions['order_details']['continue_shopping'];
    }

    /**
     * @return mixed
     */
    public function isGoogleMapEnabled()
    {
        return $this->_thankYouPageOptions['google_map']['enable'];
    }

    /**
     * @return mixed
     */
    public function isWesupplyIntegrationEnabled()
    {
        if ($this->isWesupplyModuleEnabled()) {
            $enabledWeSupply = $this->scopeConfig->getValue(
                'wesupply_api/integration/wesupply_enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $enabledNotificationSignupBox = $this->scopeConfig->getValue(
                'wesupply_api/step_4/checkout_page_notification',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($enabledWeSupply && $enabledNotificationSignupBox) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function isOrderInfoEnabled()
    {
        return $this->_thankYouPageOptions['order_info']['enable'];
    }

    /**
     * @return string
     */
    public function showCustomerInfo()
    {
        return $this->_thankYouPageOptions['order_info']['customer_info'];
    }

    /**
     * @return string
     */
    public function showProductInfo()
    {
        return $this->_thankYouPageOptions['order_info']['product_info'];
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->_thankYouPageOptions['order_details']['title'];
    }

    /**
     * @return string
     */
    public function getPageSubTitle()
    {
        return $this->_thankYouPageOptions['order_details']['subtitle'];
    }

    /**
     * @return string
     */
    public function getOrderDescription()
    {
        return $this->_thankYouPageOptions['order_details']['description'];
    }

    /**
     * @return string
     */
    public function getOrderDetailsSortOrder()
    {
        return isset($this->_thankYouPageOptions['order_details']['sort_order']) ? $this->_thankYouPageOptions['order_details']['sort_order'] : 0;
    }

    /**
     * @return int
     */
    public function getGoogleMapSortOrder()
    {
        return isset($this->_thankYouPageOptions['google_map']['sort_order']) ? $this->_thankYouPageOptions['google_map']['sort_order'] : 0;
    }

    /**
     * @return int
     */
    public function getWesupplyIntegrationSortOrder()
    {
        return isset($this->_thankYouPageOptions['wesupply_integration']['sort_order']) ? $this->_thankYouPageOptions['wesupply_integration']['sort_order'] : 0;
    }

    /**
     * @return int
     */
    public function getOrderInfoSortOrder()
    {
        return isset($this->_thankYouPageOptions['order_info']['sort_order']) ? $this->_thankYouPageOptions['order_info']['sort_order'] : 0;
    }

    /**
     * @return bool|string
     */
    public function getGoogleApiKey()
    {
        return isset($this->_thankYouPageOptions['google_map']['api_key']) ? trim($this->_thankYouPageOptions['google_map']['api_key']) : false;
    }

    /**
     * @return mixed
     */
    public function getGoogleMapSettings()
    {
        return $this->_thankYouPageOptions['google_map'];
    }

    /**
     * @return string
     */
    public function getOrderDetailTemplate()
    {
        if ($this->isOrderDetailsEnabled()) {
            $fullActionName = $this->_request->getFullActionName();
            $template = 'WeltPixel_ThankYouPage::success.phtml';
            switch ($fullActionName) {
                case 'multishipping_checkout_success':
                    $template = 'WeltPixel_ThankYouPage::multishipping/success.phtml';
                    break;
            }


            return $template;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCreateAccountTemplate()
    {
        if ($this->isCreateAccountEnabled()) {
            return 'WeltPixel_ThankYouPage::registration.phtml';
        }

        return '';
    }

    /**
     * @return boolean
     */
    public function isCreateAccountEnabled()
    {
        return $this->_thankYouPageOptions['create_account']['enable'];
    }

    /**
     * @return string
     */
    public function getCreateAccountDescription()
    {
        return $this->_thankYouPageOptions['create_account']['description'];
    }

    /**
     * @return string
     */
    public function getCreateAccountEmailLabel()
    {
        return $this->_thankYouPageOptions['create_account']['email_label'];
    }

    /**
     * @return string
     */
    public function getCreateAccountAfterCreationLabel()
    {
        return $this->_thankYouPageOptions['create_account']['after_creation_message'];
    }

    /**
     * @return string
     */
    public function getCreateAccountSortOrder()
    {
        return isset($this->_thankYouPageOptions['create_account']['sort_order']) ? $this->_thankYouPageOptions['create_account']['sort_order'] : 0;
    }

    /**
     * @return boolean
     */
    public function isNewsletterSubscribeEnabled()
    {
        return $this->_thankYouPageOptions['newsletter_subscribe']['enable'];
    }

    /**
     * @return boolean
     */
    public function getNewsletterSubscribeDescription()
    {
        return $this->_thankYouPageOptions['newsletter_subscribe']['description'];
    }

    /**
     * @return boolean
     */
    public function getNewsletterSubscribeSortOrder()
    {
        return isset($this->_thankYouPageOptions['newsletter_subscribe']['sort_order']) ? $this->_thankYouPageOptions['newsletter_subscribe']['sort_order'] : 0;
    }

    /**
     * @return boolean
     */
    public function isCustomBlockEnabled()
    {
        return $this->_thankYouPageOptions['custom_block']['enable'];
    }

    /**
     * @return string
     */
    public function getCheckoutBlockId()
    {
        return $this->_thankYouPageOptions['custom_block']['block_id'];
    }

    /**
     * @return string
     */
    public function getCustomBlockSortOrder()
    {
        return isset($this->_thankYouPageOptions['custom_block']['sort_order']) ? $this->_thankYouPageOptions['custom_block']['sort_order'] : 0;
    }

    /**
     * @return array
     */
    public function getAvailableBlockElements()
    {
        $fullActionName = $this->_request->getFullActionName();
        $checkoutSuccessBlock = 'checkout.success';
        switch ($fullActionName) {
            case 'multishipping_checkout_success':
                $checkoutSuccessBlock = 'checkout_success';
                break;
        }

        $blocks = [];
        $blocksForOutput = [];

        if ($this->isOrderDetailsEnabled()) {
            $blocksForOutput[$checkoutSuccessBlock] = $this->getOrderDetailsSortOrder();
        }

        if ($this->isGoogleMapEnabled()) {
            $blocksForOutput['weltpixel.checkout.google.map'] = $this->getGoogleMapSortOrder();
        }

        if ($this->isOrderInfoEnabled()) {
            $blocksForOutput['weltpixel.checkout.order.info'] = $this->getOrderInfoSortOrder();
        }

        if ($this->isCreateAccountEnabled()) {
            $blocksForOutput['checkout.registration'] = $this->getCreateAccountSortOrder();
        }

        if ($this->isCustomBlockEnabled()) {
            $blocksForOutput['weltpixel.checkout.block'] = $this->getCustomBlockSortOrder();
        }

        if ($this->isNewsletterSubscribeEnabled()) {
            $blocksForOutput['weltpixel.checkout.newsletter'] = $this->getNewsletterSubscribeSortOrder();
        }

        asort($blocksForOutput);

        // Insert WeSupply Notification Signup Box
        if ($this->isWesupplyIntegrationEnabled()) {
            $blocks = $this->insertAfter($blocksForOutput, $checkoutSuccessBlock, ['wesupply.notification' => 0]);
        } else {
            $blocks = $blocksForOutput;
        }

        return array_keys($blocks);
    }

    /**
     * Insert a key/value pair after a specific key in given array
     * If key doesn't exist, value is prepend to the beginning of the array
     *
     * @param array $array
     * @param $key
     * @param array $new
     * @return array
     */
    private function insertAfter(array $array, $key, array $new)
    {
        $keys = array_keys($array);
        $index = array_search($key, $keys);
        $pos = false === $index ? 0 : $index + 1;

        return array_merge(
            array_slice($array, 0, $pos),
            $new,
            array_slice($array, $pos)
        );
    }

    /**
     * @return string
     */
    public function getRegistrationTemplate()
    {
        $template = 'WeltPixel_ThankYouPage/registration';
        return $template;
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }

    /**
     * Check if WeSupply Integration module is enabled
     *
     * @return mixed
     */
    public function isWesupplyModuleEnabled()
    {
        if (
            $this->isModuleEnabled('WeSupply_Toolbox') &&
            $this->isOutputEnabled('WeSupply_Toolbox')
        ) {
            return true;
        }

        return false;
    }
}
