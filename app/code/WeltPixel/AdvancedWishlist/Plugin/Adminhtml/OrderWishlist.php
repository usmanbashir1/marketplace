<?php

namespace WeltPixel\AdvancedWishlist\Plugin\Adminhtml;

use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\Wishlist as SidebarWishlist;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;
use Magento\Customer\Api\CustomerRepositoryInterface;
use WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider;

class OrderWishlist
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
     * @var MultipleWishlistProvider
     */
    protected $multipleWishlistProvider;

    /**
     * OrderWishlist constructor.
     * @param WishlistHelper $helper
     * @param QuoteSession $quoteSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param MultipleWishlistProvider $multipleWishlistProvider
     */
    public function __construct(
        WishlistHelper $helper,
        QuoteSession $quoteSession,
        CustomerRepositoryInterface $customerRepository,
        MultipleWishlistProvider $multipleWishlistProvider
    )
    {
        $this->_helper = $helper;
        $this->quoteSession = $quoteSession;
        $this->customerRepository = $customerRepository;
        $this->multipleWishlistProvider = $multipleWishlistProvider;
    }

    /**
     * @param SidebarWishlist $subject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeGetItemCollection(
        SidebarWishlist $subject
    ){
        $customerId = (int)$this->quoteSession->getCustomerId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $websiteId = $customer->getWebsiteId();
            $isMultiWishlistEnabled = $this->_helper->isMultiWishlistEnabled($websiteId);
            if ($isMultiWishlistEnabled) {
                $collection = $this->multipleWishlistProvider->getWishlistItemsForCustomer($customerId);
                $subject->setData('item_collection', $collection);
            }
        }

        return [];
    }
}