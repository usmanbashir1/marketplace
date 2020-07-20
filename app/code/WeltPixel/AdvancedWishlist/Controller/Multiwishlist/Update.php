<?php
namespace WeltPixel\AdvancedWishlist\Controller\Multiwishlist;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Wishlist\Model\WishlistFactory;

class Update extends Action
{

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Update constructor.
     * @param WishlistFactory $wishlistFactory
     * @param CustomerSession $customerSession
     * @param Context $context
     */
    public function __construct(
        WishlistFactory $wishlistFactory,
        CustomerSession $customerSession,
        Context $context
    ) {
        parent::__construct($context);
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_redirect('/');
            return;
        }

        $result = [
            'result' => false,
            'reload' => true
        ];
        $customerId = $this->customerSession->getCustomerId();

        if (!$customerId) {
            return $this->prepareResult($result);
        }

        $wishlistId = $this->getRequest()->getParam('wishlist-id', null);
        $wishlistName = $this->getRequest()->getParam('wishlist-name', '');
        $wishlistModel = $this->wishlistFactory->create();
        try {
            $wishlistModel->load($wishlistId);
            $wishlistModel->setWishlistName($wishlistName);
            $wishlistModel->setCustomerId($customerId);
            if (!$wishlistModel->getSharingCode()) {
                $wishlistModel->generateSharingCode();
            }
            $wishlistModel->save();
            $result['result'] = true;
            if ($wishlistId) {
                $result['reload'] = false;
            }

        } catch (\Exception $e) {
            $result['reload'] = false;
            $result['msg'] = $e->getMessage();
            return $this->prepareResult($result);
        }

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
