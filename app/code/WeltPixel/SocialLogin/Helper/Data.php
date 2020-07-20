<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Helper;

/**
 * Class Data
 * @package WeltPixel\SocialLogin\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const REFERER_STORE_PARAM_NAME = 'sociallogin_referer_store';
    const REFERER_QUERY_PARAM_NAME = 'sociallogin_referer';

    /**
     * @var string
     */
    protected $_configSectionId = 'weltpixel_sociallogin';
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var int
     */
    protected $_storeId;
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $magHelper;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Magento\Store\Model\StoreRepository
     */
    protected $_storeRepository;

    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $_urlFactory;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Helper\Data $magHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\StoreRepository $storeRepository
     * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Helper\Data $magHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\StoreRepository $storeRepository,
        \Magento\Framework\UrlFactory $urlFactory
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);

        $this->magHelper = $magHelper;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->customerUrl = $customerUrl;
        $this->cookieManager = $cookieManager;
        $this->backendHelper = $backendHelper;
        $this->_storeRepository = $storeRepository;
        $this->_urlFactory = $urlFactory;
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->getGeneralConfig('enabled', $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($field, $storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_sociallogin/general/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $type
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getSocialConfig($type, $field, $storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_sociallogin/' . $type . '/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $provider
     * @param bool $byRequest
     * @return bool|mixed|string
     */
    public function getCallback($provider, $byRequest = false)
    {
        $url = $this->storeManager
            ->getStore()
            ->getUrl('sociallogin/account/login', ['type' => $provider, 'key' => null, '_nosid' => true]);

        $url = str_replace(
            '/' . $this->magHelper->getAreaFrontName() . '/',
            '/',
            $url
        );

        if (false !== ($length = stripos($url, '?'))) {
            $url = substr($url, 0, $length);
        }

        if ($byRequest) {
            if ($this->getConfig('web/seo/use_rewrites')) {
                $url = str_replace('index.php/', '', $url);
            }
        }

        return $url;
    }

    /**
     * @param $path
     * @param null $store
     * @param null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        $redirectUrl = '';
        $links = [];
        if ($referer = $this->_getRequest()->getParam(\Magento\Customer\Model\Url::REFERER_QUERY_PARAM_NAME)) {
            $links[] = $this->urlDecoder->decode($referer);
        }
        if ($referer = $this->getReferer()) {
            $links[] = $referer;
        }
        foreach ($links as $url) {
            $redirectUrl = $this->_createUrl()->getRebuiltUrl($url);
        }

        if (!$redirectUrl) {
            $redirectUrl = $this->customerUrl->getDashboardUrl();
        }

        return $redirectUrl;
    }

    /**
     * @return null|string
     */
    public function getCookieRefererLink()
    {
        return $this->cookieManager->getCookie(self::REFERER_QUERY_PARAM_NAME);
    }

    /**
     * Receive config section id
     *
     * @return string
     */
    public function getConfigSectionId()
    {
        return $this->_configSectionId;
    }

    /**
     * @param $provider
     * @param bool $byRequest
     * @return bool|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCallbackUri($provider, $byRequest = false)
    {
        $request = $this->_getRequest();
        $websiteCode = $request->getParam('website');

        $displayedWebsite = $this->storeManager->getWebsite($byRequest ? $websiteCode : null);
        if (!$displayedWebsite->getId()) {
            $websites = $this->storeManager->getWebsites(true);
            foreach ($websites as $website) {
                $defaultStoreId = $website
                    ->getDefaultGroup()
                    ->getDefaultStoreId();

                if ($defaultStoreId) {
                    $displayedWebsite = $website;
                    break;
                }
            }
        }

        $storeIds = [];
        $groups = $displayedWebsite->getGroups();
        foreach ($groups as $group) {
            $stores = $group->getStores();
            foreach ($stores as $storeView) {
                $storeIds[] = $storeView->getCode();
            }
        }

        $stores = $this->_storeRepository->getList();

        $showStoreCode = $this->scopeConfig->getValue('web/url/use_store');
        if ($showStoreCode) {
            $storeIds = [];
            foreach ($stores as $store) {
                if ($store->getCode() != 'admin') {
                    $storeIds[] = $store->getId();
                }
            }
        }

        $urlArr = [];
        foreach ($storeIds as $storeId) {
            $url = $this->storeManager->getStore($storeId)->getBaseUrl() . 'sociallogin/account/login/type/' . $provider . '/';
            if (false !== ($length = stripos($url, '?'))) {
                $url = substr($url, 0, $length);
            }
            $url = preg_replace('~(\?|/)key/[^&]*~', '$1', $url);
            $url = str_replace('http://', 'https://', $url);

            if ($byRequest) {
                if ($this->getConfig('web/seo/use_rewrites')) {
                    $url = str_replace('index.php/', '', $url);
                }
            }

            $urlArr[] = $url;
        }

        $urlArr = array_unique($urlArr);

        return $urlArr;
    }

    /**
     * @param bool $value
     * @return mixed
     */
    public function refererStore($referer = false)
    {
        $sessionData = $this->customerSession->getData(self::REFERER_STORE_PARAM_NAME);
        if ($referer) {
            $this->customerSession->setData(self::REFERER_STORE_PARAM_NAME, $referer);
        } elseif ($referer === null) {
            $this->customerSession->unsetData(self::REFERER_STORE_PARAM_NAME);
        }

        return $sessionData;
    }

    /**
     * @return bool
     */
    public function isGlobalScope()
    {
        return $this->customer->getSharingConfig()->isGlobalScope();
    }

    /**
     * @param bool $email
     * @return bool
     */
    public function isTempMail($email = false)
    {
        if (!$email) {
            if ($this->customerSession->isLoggedIn()) {
                $email = $this->customerSession->getCustomer()->getEmail();
            }
        }
        return (bool)(strpos($email, self::TEMP_EMAIL_PREFIX) === 0);
    }

    /**
     * @param bool $referer
     * @return mixed
     */
    public function getReferer($referer = false)
    {
        $customerReferer = $this->customerSession->getData(self::REFERER_QUERY_PARAM_NAME);
        if ($referer) {
            $this->customerSession->setData(self::REFERER_QUERY_PARAM_NAME, $referer);
        } elseif ($referer === null) {
            $this->customerSession->unsetData(self::REFERER_QUERY_PARAM_NAME);
        }

        return $customerReferer;
    }

    /**
     * @return array
     */
    public function getSkipModulesReferer()
    {
        return ['customer/account', 'sociallogin/account'];
    }

    /**
     * @return mixed
     */
    public function isSecure()
    {
        $isSecure = $this->scopeConfig->getValue('web/secure/use_in_frontend');

        return $isSecure;
    }

    /**
     * @return bool
     */
    public function isGuestCheckoutEnabled()
    {
        return $this->scopeConfig->getValue('checkout/options/guest_checkout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return \Magento\Framework\UrlInterface
     */
    protected function _createUrl()
    {
        return $this->_urlFactory->create();
    }
}
