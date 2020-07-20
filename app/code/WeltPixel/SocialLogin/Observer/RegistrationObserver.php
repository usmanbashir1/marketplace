<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use WeltPixel\SocialLogin\Helper\Data as SlHelper;

/**
 * Class RegistrationObserver
 * @package WeltPixel\SocialLogin\Observer
 */
class RegistrationObserver implements ObserverInterface
{
    /**
     * @var SlHelper
     */
    protected $_slHelper;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * RegistrationObserver constructor.
     * @param SlHelper $slHelper
     * @param ObjectManagerInterface $objectManager
     * @param Session $customerSession
     * @param RequestInterface $httpRequest
     */
    public function __construct(
        SlHelper $slHelper,
        ObjectManagerInterface $objectManager,
        Session $customerSession,
        RequestInterface $httpRequest
    ) {
        $this->_slHelper = $slHelper;
        $this->objectManager = $objectManager;
        $this->session = $customerSession;
        $this->request = $httpRequest;
    }

    public function execute(Observer $observer)
    {
        if (!$this->_slHelper->isEnabled()) {
            return;
        }

        $data = $this->session->getData('sociallogin');
        
        if (!empty($data['provider']) && !empty($data['timeout']) && $data['timeout'] > time()) {
            $model = $this->objectManager->get('WeltPixel\SocialLogin\Model\\'. ucfirst($data['provider']));
            
            $customerId = null;
            if ($customer = $observer->getCustomer()) {
                $customerId = $customer->getId();
            }

            if ($customerId) {
                $model->setUserData($data);
                $model->setCustomerByUser($customerId);
            }

        }

        $redirectUrl = $this->_slHelper->getRedirectUrl();
        $this->request->setParam(\Magento\Framework\App\Response\RedirectInterface::PARAM_NAME_SUCCESS_URL, $redirectUrl);
    }
}
