<?php
namespace WeltPixel\AdvancedWishlist\Controller\Multiwishlist;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Wishlist\Model\WishlistFactory;

class Delete extends Action
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
            'result' => false
        ];
        $customerId = $this->customerSession->getCustomerId();
        $wishlistId = $this->getRequest()->getParam('wishlistId', null);

        if (!$customerId || !$wishlistId) {
            return $this->prepareResult($result);
        }

        $wishlistModel = $this->wishlistFactory->create();
        try {
            $wishlistModel->load($wishlistId);
            $wishlistModel->delete();
            $result['result'] = true;
        } catch (\Exception $e) {
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
