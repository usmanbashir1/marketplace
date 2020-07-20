<?php

namespace WeltPixel\AdvancedWishlist\Plugin\Adminhtml;

use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Wishlist\Model\Wishlist as WishlistModel;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Wishlist
{
    /**
     * @var WishlistHelper
     */
    protected $_helper;

    /**
     * @var QuoteSession
     */
    protected $quoteSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param WishlistAddController $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundExecute(
        WishlistAddController $subject,
        \Closure $proceed
    ){
        $result = $proceed();
        if ($subject->getRequest()->getParam('ajax') == 1 && $this->_helper->isAjaxWishlistEnabled()) {
            return $subject->getResponse()->representJson(json_encode([
                'result' => true
            ]));

        }
        return $result;
    }

    /**
     * Wishlist constructor.
     * @param WishlistHelper $helper
     * @param QuoteSession $quoteSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        WishlistHelper $helper,
        QuoteSession $quoteSession,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->_helper = $helper;
        $this->quoteSession = $quoteSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param WishlistModel $subject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeGetName(
        WishlistModel $subject
    ){
        $customerId = (int)$this->quoteSession->getCustomerId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $websiteId = $customer->getWebsiteId();
            $isMultiWishlistEnabled = $this->_helper->isMultiWishlistEnabled($websiteId);
            if ($isMultiWishlistEnabled) {
                $subject->setData('name', $subject->getData('wishlist_name'));
            }
        }

        return [];
    }
}