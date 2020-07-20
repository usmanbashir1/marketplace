<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */


namespace WeltPixel\SocialLogin\Controller;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

/**
 * Class AbstractAccount
 * @package WeltPixel\SocialLogin\Controller
 */
abstract class AbstractAccount extends \Magento\Framework\App\Action\Action
{
    /**
     * @var
     */
    public $type;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \WeltPixel\SocialLogin\Helper\Data
     */
    protected $slHelper;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\View\Layout\Interceptor
     */
    protected $layout;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @type
     */
    protected $cookieMetadataManager;

    /**
     * @type
     */
    protected $cookieMetadataFactory;

    /**
     * AbstractAccount constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \WeltPixel\SocialLogin\Helper\Data $slHelper
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\Layout\Interceptor $layout
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \WeltPixel\SocialLogin\Helper\Data $slHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Layout\Interceptor $layout
    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->slHelper = $slHelper;
        $this->storeManager = $storeManager;
        $this->layout = $layout;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @param $type
     */
    protected function _setType($type) {
        $this->type = $type;
    }

    /**
     * @param $type
     * @return mixed
     */
    protected function _getModel() {
        $className = 'WeltPixel\SocialLogin\Model\\' . ucfirst($this->type);
        $exist = class_exists($className);
        if(!$exist) {
            $this->_windowClose();
        }

        return $this->_objectManager->get($className);
    }

    /**
     * @param $userProfile
     * @return bool
     */
    public function createCustomerProcess($userProfile)
    {

        $user = array_merge([
            'email'      => $userProfile['email'],
            'firstname'  => $userProfile['firstname'],
            'lastname'   => $userProfile['lastname'],
            'identifier' => $userProfile['user_id'],
            'type'       => $this->type
        ], $this->getUserData($userProfile));

        return $this->createCustomer($user, $this->type);
    }

    /**
     * @param $user
     * @return bool
     */
    public function createCustomer($user)
    {
        $customer = $this->model->getCustomerByEmail($user['email']);
        if (!$customer->getId()) {
            try {
                $customer = $this->model->createCustomerSocial($user, $this->storeManager->getStore());
            } catch (\Exception $e) {
                $this->emailRedirect();

                return false;
            }
        } else {
            $this->model->setUser($user['user_id'], $customer->getId(), $this->type);
        }

        return $customer;
    }


    /**
     * @param $profile
     * @return array
     */
    protected function getUserData()
    {
        return [];
    }

    /**
     * @param $msg
     * @param bool $needTranslate
     * @return $this
     */
    public function emailRedirect()
    {
        $this->_redirect('customer/account/login');

        return $this;
    }

    protected function _windowClose()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(json_encode([
                'windowClose' => true
            ]));
        } else {
            $this->getResponse()->setBody($this->attacheJs('window.close();'));
        }
    }

    /**
     * @param $customer
     */
    protected function _dispatchRegisterSuccess($customer)
    {
        $this->_eventManager->dispatch(
            'customer_register_success',
            ['account_controller' => $this, 'customer' => $customer]
        );
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * @param       $url
     * @param array $params
     *
     * @return string
     */
    protected function _getUrl($url, $params = [])
    {
        return $this->_url->getUrl($url, $params);
    }

    /**
     * @return \WeltPixel\SocialLogin\Helper\Data
     */
    protected function _slHelper()
    {
        return $this->slHelper;
    }

    /**
     * @param $content
     * @return string
     */
    protected function attacheJs($content)
    {
        return '<html><head></head><body><script type="text/javascript">' . $content . '</script></body></html>';
    }

    /**
     * @param null $content
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function _appendJs($content = null)
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($content);
    }

    /**
     * @param $customer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function refresh($customer)
    {

        if ($customer && $customer->getId()) {
            $this->customerSession->setCustomerAsLoggedIn($customer);
            $this->customerSession->regenerateId();

            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(
                PhpCookieManager::class
            );
        }

        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(
                CookieMetadataFactory::class
            );
        }

        return $this->cookieMetadataFactory;
    }


}
