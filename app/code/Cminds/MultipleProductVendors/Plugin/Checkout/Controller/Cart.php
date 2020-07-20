<?php
namespace Cminds\MultipleProductVendors\Plugin\Checkout\Controller;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;

class Cart
{

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Plugin constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ProductRepositoryInterface $productRepository
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductRepositoryInterface $productRepository,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManager
    ) {
        $this->_objectManager = $objectManager;
        $this->productRepository = $productRepository;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * beforeAddProduct
     * Prevent main vendor products from being addded to the cart.
     *
     * @param $subject
     * @param callable $proceed
     *
     * @return array
     * @throws LocalizedException
     */
    public function aroundExecute($subject, callable $proceed)
    {
        
        $product = false;
        $productId = (int)$subject->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get(
                StoreManagerInterface::class
            )->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                $product = false;
            }
        }

        // check if main vendor product
        if( $product
            && true === (bool) $product->getData('main_product')
        ){
            $this->messageManager->addErrorMessage(
                __('Please select a product')
            );

            // redirect to product page on addTo Cart
            $result['backUrl'] = $product->getProductUrl();
            $subject->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')
                    ->jsonEncode($result)
            );
        } else {
            return $proceed();
        }
    }
}