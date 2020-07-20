<?php
namespace WeltPixel\AdvancedWishlist\Controller\Multiwishlist;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider;

class Get extends Action
{

    /**
     * @var MultipleWishlistProvider
     */
    protected $multipleWishlistProvider;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Update constructor.
     * @param MultipleWishlistProvider $multipleWishlistProvider
     * @param CustomerSession $customerSession
     * @param Context $context
     */
    public function __construct(
        MultipleWishlistProvider $multipleWishlistProvider,
        CustomerSession $customerSession,
        Context $context
    ) {
        parent::__construct($context);
        $this->multipleWishlistProvider = $multipleWishlistProvider;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_redirect('/');
            return;
        }

        $result = [
            'result' => false
        ];
        $customerId = $this->customerSession->getCustomerId();

        if (!$customerId) {
            return $this->prepareResult($result);
        }

        $wishlists = $this->multipleWishlistProvider->getWishlists();
        $wishlistData = [];
        if (count($wishlists)) {
            foreach ($wishlists as $wishlist) {
                $wishlistData[] = [
                    'id' => $wishlist->getWishlistId(),
                    'name' => $wishlist->getWishlistName()
                ];
            }
        } else {
            $wishlistData[] = [
                'id' => 0,
                'name' => __('My Wish List')
            ];
        }

        $result['result'] = true;
        $result['wishlists'] = $wishlistData;
        return $this->prepareResult($result);
    }

    /**
     * @param array $result
     * @return string
     */
    protected function prepareResult($result)
    {
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
