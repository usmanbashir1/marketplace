<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use WeltPixel\SocialLogin\Helper\Data as SlHelper;

class LoginObserver implements ObserverInterface
{
    /**
     * @var SlHelper
     */
    protected $_slHelper;
    /**
     * @var Session
     */
    protected $_session;

    /**
     * LoginObserver constructor.
     * @param SlHelper $slHelper
     * @param Session $customerSession
     */
    public function __construct(
        SlHelper $slHelper,
        Session $customerSession
    ) {
        $this->_slHelper = $slHelper;
        $this->_session = $customerSession;
    }

    public function execute(Observer $observer)
    {
        if(!$this->_slHelper->isEnabled()) {
            return;
        }
        $redirectUrl = $this->_slHelper->getRedirectUrl();
        $this->_session->setBeforeAuthUrl($redirectUrl);
    }
}
