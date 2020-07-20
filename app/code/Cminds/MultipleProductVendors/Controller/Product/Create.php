<?php

namespace Cminds\MultipleProductVendors\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Copier as ProductCopier;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryLinkRepository;
use Magento\Catalog\Api\CategoryLinkManagementInterface;

use Cminds\Supplierfrontendproductuploader\Helper\Data as FrontendProductUploaderHelper;

class Create extends \Magento\Framework\App\Action\Action
{
    /**
      * @var LayoutFactory
      */
    protected $layoutFactory;

    /**
      * @var RawFactory
      */
    protected $resultRawFactory;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var ProductCopier
     */
    protected $productCopy;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var JsonFactory $resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var JsonFactory $resultJsonFactory
     */
    protected $frontProductUploaderHelper;

    /**
     * Product collection factory.
     *
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryLinkRepository
     */
    protected $categoryLinkRepository;

    /**
     * @var CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;

    /**
     * @var int
     */
    protected $currentStore;

    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory,
        Product $product,
        ProductCopier $copier,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        JsonFactory $resultJsonFactory,
        FrontendProductUploaderHelper $frontProductUploaderHelper,
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        CategoryLinkManagementInterface $categoryLinkManagement
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->product = $product;
        $this->productCopier = $copier;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->frontProductUploaderHelper = $frontProductUploaderHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryLinkManagement = $categoryLinkManagement;

        return parent::__construct($context);
    }

    public function execute()
    {
        $this->currentStore = $this->storeManager->getStore();

        $this->storeManager
            ->setCurrentStore(Store::DEFAULT_STORE_ID);


        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $response = [];

        $requestArray = $this->getRequest()->getParams();
        $requiredParams = [
            'prototype' => 'Targer prdocut ID',
            'price' => 'Product price',
            'qty' => 'Product quantity',
        ];
        $errors = [];

        $isProductEditPage = isset($requestArray['producteditpage']);

        foreach ($requiredParams as $paramName => $paramText) {
            if(!isset($requestArray[$paramName]) || !$requestArray[$paramName])
                $errors[] = __("%1 is a required field", $paramText) . '.';
        }

        // load product by id
        $product = $this->product->load( (int)$requestArray['prototype'] );


        if( $product
            && count($errors) == 0 )
        {
            $successMessage = "Product successfully saved";

            $currentSupplierId = $this->frontProductUploaderHelper->getSupplierId();
            // check if such vendor-product pair already exists
            $this->productCollection = $this->productCollectionFactory->create();
            $possibleProducts = $this->productCollection
                ->addAttributeToSelect(['sku', 'creator_id', 'manufacturer_code', 'main_product' ])
                ->addAttributeToFilter('manufacturer_code', $product->getData('manufacturer_code'))
                ->addAttributeToFilter('creator_id', $currentSupplierId);

            // product found
            if($possibleProducts->count()){
                $productCopy = $possibleProducts->getFirstItem();

                $successMessage = "Product successfully updated";
                // skip for product edit page
                if( false === $isProductEditPage ){
                    $this->messageManager->addNoticeMessage(
                        __("Product with this manufacturer code already exists")
                    );
                }
            }
            // create a new copy
            else {
                // remove main product flag before copying to prevent an error
                $product->setData( 'main_product', 0); // reset main product flag
                $product->setData( 'special_price', null); // reset special price
                $product->setData( 'category_ids', []); // reset categpries

                // duplicate product
                $productCopy = $this->productCopier->copy($product);
            }

            $productCopy->setPrice( (float)$requestArray['price']);

            // $productCopy->setQty( (float)$requestArray['qty']);
            if(isset($requestArray['vendor_description'])
                && $requestArray['vendor_description'])
            {
                $productCopy->setData( 'vendor_description', $requestArray['vendor_description']);
            }

            // assign to current supplier
            $productCopy->setData(
                'creator_id',
                $this->frontProductUploaderHelper->getSupplierId()
            );
            $productCopy->setStatus(
                Status::STATUS_ENABLED
            );

            /* VISIBILITY_NOT_VISIBLE causes issues with native add to cart functionality
            also an issue with item display in minicart (both were fixed)
            */
            $productCopy->setVisibility(
                // ProductVisibility::VISIBILITY_NOT_VISIBLE
                ProductVisibility::VISIBILITY_IN_SEARCH
            );

            /** Save quantity and stock data here */
            $qty = (int)$requestArray['qty'] ?: 0;
            $productStockStatus = $qty === 0 ?: 1;


            $productCopy->setWebsiteIds([$this->currentStore->getWebsiteId()]);

            if ($productCopy->getId()) {
                $stockItem = $this->stockRegistry
                    ->getStockItem($productCopy->getId());
                $stockItem
                    ->setQty($qty)
                    ->setIsInStock( (bool) $productStockStatus )
                    ->save();
            }

            $productCopy->setStockData(['qty' => $qty, 'is_in_stock' => $productStockStatus]);
            $productCopy->setQuantityAndStockStatus(['qty' => $qty, 'is_in_stock' => $productStockStatus]);

            /** Save prodcut data */
            $this->productRepository->save($productCopy);
            // $this->storeManager
            //     ->setCurrentStore( $this->currentStore->getId() );

            $this->messageManager->addSuccess(
                __($successMessage)
            );
            /** @var array $response */
            $response['success'] = true;
        } else {
            if(count($errors)){
                foreach($errors as $errorMessage ) {
                    $this->messageManager->addErrorMessage(
                        __($errorMessage)
                    );
                }
            }
            $response['success'] = false;
        }

        return $resultJson->setData($response);
    }
}
