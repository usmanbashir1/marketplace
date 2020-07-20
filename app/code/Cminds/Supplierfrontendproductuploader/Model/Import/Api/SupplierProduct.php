<?php

namespace Cminds\Supplierfrontendproductuploader\Model\Import\Api;

use Cminds\Supplierfrontendproductuploader\Api\SupplierProductInterface;
use Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface as SupplierProductDataInterface;
use Cminds\Supplierfrontendproductuploader\Api\Data\SupplierConfigurationInterface;
use Cminds\Supplierfrontendproductuploader\Helper\Data as CmDataHelper;
use Cminds\Supplierfrontendproductuploader\Model\ApiTokenFactory;
use Cminds\Supplierfrontendproductuploader\Model\Config as SupplierConfig;
use Cminds\Supplierfrontendproductuploader\Model\Data\ResultFactory as SupplierResultFactory;
use Cminds\Supplierfrontendproductuploader\Model\Product\Builder as ConfigProductBuilder;
use Cminds\Supplierfrontendproductuploader\Model\Product\Builder\Type\Configurable as ConfigurableLinkBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\AttributeInterface;
use Magento\Catalog\Api\AttributeSetRepositoryInterface as CatalogAttributeSetRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Eav\Api\AttributeRepositoryInterface as EavAttributeSetRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\CategoryList;
use Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class SupplierProduct implements SupplierProductInterface
{
    /**
     * @var ApiTokenFactory
     */
    protected $apiTokenFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CatalogAttributeSetRepositoryInterface
     */
    protected $catalogAttributeSetRepository;

    /**
     * @var EavAttributeSetRepositoryInterface
     */
    protected $eavAttributeSetRepositoryInterface;

    /**
     * @var CategoryList
     */
    protected $categoryList;

    /**
     * @var \Magento\Eav\Api\Data\AttributeSetSearchResultsInterface
     */
    protected $supplierAttributeSets = null;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var SupplierConfig
     */
    protected $supplierConfig;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var SupplierResultFactory
     */
    protected $supplierResultFactory;

    /**
     * @var ProductImageImport
     */
    protected $productImageImport;

    /**
     * @var CmDataHelper
     */
    protected $cmDataHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ConfigProductBuilder
     */
    protected $configProductBuilder;

    /**
     * @var ConfigurableLinkBuilder
     */
    protected $configurableLinkBuilder;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface|null
     */
    protected $customer = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product[]
     */
    protected $productCollection;

    /**
     * @var int
     */
    protected $currentCustomerId = null;
    protected $currentWebsiteId = null;
    protected $currentStoreId = null;

    /**
     * @var array
     */
    protected $supplierAllowedCategoryIds = null;
    protected $supplierRequiredAttributes = null;
    protected $supplierAllowedAttributes = null;

    protected $supplierAttributesBySetId = [];
    protected $supplierAttributes = [];
    protected $productProcessingErrors = [];


    /**
     * Object constructor.
     *
     * @param ApiTokenFactory                           $apiTokenFactory
     * @param SearchCriteriaBuilder                     $searchCriteriaBuilder
     * @param CatalogAttributeSetRepositoryInterface    $catalogAttributeSetRepository
     * @param EavAttributeSetRepositoryInterface        $eavAttributeSetRepositoryInterface
     * @param CategoryList                              $categoryList
     * @param ProductRepositoryInterface                $productRepository
     * @param CustomerRepositoryInterface               $customerRepository
     * @param ProductFactory                            $productFactory
     * @param SupplierConfig                            $supplierConfig
     * @param StockRegistryInterface                    $stockRegistry
     * @param ProductImageImport                        $productImageImport
     * @param CmDataHelper                              $cmDataHelper
     * @param StoreManagerInterface                     $storeManager
     * @param ProductCollectionFactory                  $productCollectionFactory
     * @param SupplierResultFactory                     $supplierResultFactory
     * @param ConfigProductBuilder                      $configProductBuilder
     * @param ConfigurableLinkBuilder                   $configurableLinkBuilder
     */
    public function __construct(
        ApiTokenFactory $apiTokenFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CatalogAttributeSetRepositoryInterface $catalogAttributeSetRepository,
        EavAttributeSetRepositoryInterface $eavAttributeSetRepositoryInterface,
        CategoryList $categoryList,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        ProductFactory $productFactory,
        SupplierConfig $supplierConfig,
        StockRegistryInterface $stockRegistry,
        ProductImageImport $productImageImport,
        CmDataHelper $cmDataHelper,
        StoreManagerInterface $storeManager,
        ProductCollectionFactory $productCollectionFactory,
        SupplierResultFactory $supplierResultFactory,
        ConfigProductBuilder $configProductBuilder,
        ConfigurableLinkBuilder $configurableLinkBuilder
    ) {
        $this->apiTokenFactory = $apiTokenFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->catalogAttributeSetRepository = $catalogAttributeSetRepository;
        $this->eavAttributeSetRepositoryInterface = $eavAttributeSetRepositoryInterface;
        $this->categoryList = $categoryList;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->productFactory = $productFactory;
        $this->supplierConfig = $supplierConfig;
        $this->stockRegistry = $stockRegistry;
        $this->productImageImport = $productImageImport;
        $this->cmDataHelper = $cmDataHelper;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->supplierResultFactory = $supplierResultFactory;
        $this->configProductBuilder = $configProductBuilder;
        $this->configurableLinkBuilder = $configurableLinkBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSetList($userAccessToken){
        $this->canAccess($userAccessToken);

        return $this->getSupplierAttributeSets();
    }


    /**
     * Get attributes sets available fr supplier
     *
     * @return \Magento\Eav\Api\Data\AttributeSetSearchResultsInterface
     */
    protected function getSupplierAttributeSets(){
        if(null === $this->supplierAttributeSets){
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('available_for_supplier',1)
                ->create();
            $this->supplierAttributeSets = $this->catalogAttributeSetRepository->getList($searchCriteria);
        }
        return $this->supplierAttributeSets;
    }


    /**
     * {@inheritdoc}
     */
    public function getAttributesList($userAccessToken, $attributeSetId){
        $this->canAccess($userAccessToken);

        return $this->getSupplierAttributesBySet($attributeSetId);
    }

    /**
     * Get supplier attributes by set id
     * @param int $attributeSetId
     * @return \Magento\Eav\Api\Data\AttributeSearchResultsInterface
     * @throws NoSuchEntityException
     */

    protected function getSupplierAttributesBySet($attributeSetId ){

        // check if attribute set is available
        $availableSets = $this->getSupplierAttributeSets();

        $setIdFound = false;
        foreach($availableSets->getItems() as $setItem){
            if( (int)$attributeSetId == $setItem->getId() ){
                $setIdFound = true;
                $attributeSetId = $setItem->getId();
                break;
            }
        }

        if(false === $setIdFound){
            throw new NoSuchEntityException(
                __('Attribute set not found')
            );
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('attribute_set_id', $attributeSetId)
            ->addFilter('available_for_supplier', 1)
            ->create();

        // @toDo: check if we need to remove some fields from the response data
        return $this->eavAttributeSetRepositoryInterface->getList(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getCategoryList($userAccessToken){
        $this->canAccess($userAccessToken);
        return $this->getSupplierAllowedCategories();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteProducts($userAccessToken, array $productSkuArray){
        $this->canAccess($userAccessToken);

        $removedProducts = [];

        if(count($productSkuArray)){
            $collection = $this->productCollectionFactory->create();
            $collection
                ->addAttributeToSelect('creator_id')
                ->addAttributeToSelect('sku')
                ->addAttributeToFilter('sku', array('in' => $productSkuArray))
                ->addAttributeToFilter('creator_id', array('eq' => $this->currentCustomerId));

            foreach($collection as $product){
                $this->productRepository->delete($product);
                $removedProducts[] = $product->getSku();
            }
        }

        $result[] = $this->supplierResultFactory->create()
            ->setResultKey('removed')
            ->setResultData($removedProducts);

        $skippedProducts = array_diff($productSkuArray, $removedProducts);

        $result[] = $this->supplierResultFactory->create()
            ->setResultKey('skipped')
            ->setResultData($skippedProducts);

        return $result;
    }


    /**
     * {@inheritdoc}
     */
    public function saveProducts($userAccessToken, array $products){
        $this->canAccess($userAccessToken);

        $result = $this->processObjects($products);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createConfiguration($userAccessToken, array $products){
        $this->canAccess($userAccessToken);

        $result = $this->processObjects($products);

        return $result;
    }


    /**
     * @param SupplierProductDataInterface[]|SupplierConfigurationInterface[] $products
     * @return \Cminds\Supplierfrontendproductuploader\Model\Data\Result[]
     */
    protected function processObjects(array $products){

        // needed to import to default store,
        // if not specified will set data on website level
        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);
//        $this->storeManager->setCurrentStore($this->getCustomerStoreId());

        $this->getExistingProducts($products);

        $resultProducts = [];
        $resultErrors = [];

        foreach($products as $dataKey => $productData){
            try {
                if ($productData instanceof SupplierProductDataInterface) {
                    $productModel = $this->processProductSave($productData);
                } elseif ($productData instanceof SupplierConfigurationInterface) {
                    $productModel = $this->processConfigVariationSave($productData);
                } else {
                    throw new NoSuchEntityException(
                        __('Incorrect instance type')
                    );
                }

                $resultProducts[] = $productModel->getSku();

            }catch (LocalizedException $e){
                $resultErrors[] = $this->addKeyToErrorMessage($dataKey, $e->getMessage());
            } catch ( NoSuchEntityException $e){
                $resultErrors[] = $this->addKeyToErrorMessage($dataKey, $e->getMessage());
            } catch (\Exeption $e){
                $resultErrors[] = $this->addKeyToErrorMessage($dataKey, $e->getMessage());
            }
        }

        $result[] = $this->supplierResultFactory->create()
            ->setResultKey('processed')
            ->setResultData($resultProducts);

        $result[] = $this->supplierResultFactory->create()
            ->setResultKey('errors')
            ->setResultData($resultErrors);

        return $result;
    }


    /**
     * @param SupplierConfigurationInterface $productData
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function processConfigVariationSave(SupplierConfigurationInterface $productData){
        $parentProduct = $this->findProduct($productData->getSku());
        $variationDataArray = $productData->toArray();

        if(null === $parentProduct){
            throw new NoSuchEntityException(
                __(
                    'Product not found. SKU: "%1"', $productData->getSku()
                )
            );
        }

        if( Configurable::TYPE_CODE !== $parentProduct->getTypeId() ){
            throw new LocalizedException(
                __('Parent product must be type configurable')
            );
        }

        // validation
        if( !$variationDataArray[SupplierConfigurationInterface::NAME]){
            $variationDataArray[SupplierConfigurationInterface::NAME] = $parentProduct->getName();
        }
        if( !$variationDataArray[SupplierConfigurationInterface::QTY]){
            $variationDataArray[SupplierConfigurationInterface::QTY] = 0;
        }
        if( !$variationDataArray[SupplierConfigurationInterface::WEIGHT]){
            $variationDataArray[SupplierConfigurationInterface::WEIGHT] = $parentProduct->getWeight();
        }

        if( count($variationDataArray[SupplierConfigurationInterface::ATTRIBUTES]) == 0 ){
            throw new LocalizedException(
                __('No variation attributes provided')
            );
        }

        $parentVariationAttributes = $parentProduct->getTypeInstance()->getConfigurableAttributesAsArray($parentProduct);
        $requiredAttributes = [];
        foreach ($parentVariationAttributes as $attributeArray){
            foreach($attributeArray['options'] as $attributeValue){
                $requiredAttributes[$attributeArray['attribute_code']][] = $attributeValue['value'];
            }
        }

        $passedAttributes = [];
        $skuAppendix = '';
        // check attributes values
        foreach ($variationDataArray[SupplierConfigurationInterface::ATTRIBUTES] as $passedAttribute){
            $passedAttrCode = $passedAttribute[AttributeInterface::ATTRIBUTE_CODE];
            $passedAttrValue = $passedAttribute[AttributeInterface::VALUE];

            $skuAppendix .= "-{$passedAttrCode}-$passedAttrValue";

            $passedAttributes[$passedAttrCode] = $passedAttrValue;
            $allowedValues = isset($requiredAttributes[$passedAttrCode]) ?
                $requiredAttributes[$passedAttrCode] : null;

            if($allowedValues){
                if(!in_array($passedAttrValue,$allowedValues)){
                    throw new NoSuchEntityException(
                        __('Incorrect attribute value for attribute code: "%1"', $passedAttrCode)
                    );
                }
            }
        }

        // check if all variation attributes are present
        $diff = array_diff(array_keys($requiredAttributes),array_keys($passedAttributes));
        if(!empty($diff)){
            throw new LocalizedException(
                __('Required attributes absent: "%1".' . implode(', ', $diff))
            );
        }

        // now create a simple product
        $productModel = $this->initializeNewProduct(
            Type::TYPE_SIMPLE,
            $parentProduct->getAttributeSetId()
        );

        /** copy parent attribute values */
        $parentAttributes = $parentProduct->getTypeInstance()
            ->getEditableAttributes($parentProduct);
        foreach ($parentAttributes as $attribute) {
            if ($attribute->getIsUnique()
                || $attribute->getAttributeCode() == 'url_key'
                || $attribute->getFrontend()->getInputType() == 'gallery'
                || $attribute->getFrontend()->getInputType() == 'media_image'
                || !$attribute->getIsVisible()
            ) {
                continue;
            }

            $productModel->setData(
                $attribute->getAttributeCode(),
                $parentProduct->getData(
                    $attribute->getAttributeCode()
                )
            );
        }

        $productModel->setWebsiteIds(
            $parentProduct->getWebsiteIds()
        );

        $productModel->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE);



        $productModel->setSku($parentProduct->getSku() . $skuAppendix);
        $productModel->setName($variationDataArray[SupplierConfigurationInterface::NAME]);
        $productModel->setWeight($variationDataArray[SupplierConfigurationInterface::WEIGHT]);

//                    $productModel
//                        ->setOptions([]) //this line is important. Without it the Product SaveHandler throws exception.
//                        ->validate();

        // @toDo: check for another method to set attribute value, may not be correct to use setData
        foreach ($variationDataArray[SupplierConfigurationInterface::ATTRIBUTES] as $passedAttribute){
            $productModel->setData(
                $passedAttribute[AttributeInterface::ATTRIBUTE_CODE],
                $passedAttribute[AttributeInterface::VALUE]
            );
        }

        $productModel = $this->productRepository->save($productModel);
        // set qty
        $this->setProductQty($productModel, $variationDataArray[SupplierConfigurationInterface::QTY]);

        /** use builder to make new links for configurable products */
        $this->configurableLinkBuilder->addNewLink($parentProduct, $productModel);

        return $productModel;
    }

    /**
     * @param  SupplierProductDataInterface $productData
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function processProductSave(SupplierProductDataInterface $productData){
        // start data validation block
        /** check if required attributes are present */
        $updatingProductFlag = true;

        $dataArray = $productData->toArray();
        if(isset($dataArray[SupplierProductDataInterface::ATTRIBUTE_SET_ID])){
            $attributeSetId = $dataArray[SupplierProductDataInterface::ATTRIBUTE_SET_ID];
            $this->mapSupplierAttributesBySet($attributeSetId);
            $attributeMap = $this->supplierAttributesBySetId[$attributeSetId];
        } else {
            throw new LocalizedException(
                __('Attribute set id not specified')
            );
        }


        // check if all required attributes are present
        // maybe we should remove the required check on attributes when updating product
//                if(false === $updatingProductFlag){
        $this->validateKeys(array_keys($dataArray), $attributeMap );
//                }

        // remove all attributes, that aren't allowed
        $dataArray = $this->filterData($dataArray, $attributeMap );

        // validate attribute values
        $this->validateAttributeValues(
            $dataArray,
            $this->supplierAttributesBySetId[$attributeSetId],
            $this->supplierAttributes
        );

        //check categories
        $this->validateProductCategories($dataArray[SupplierProductDataInterface::CATEGORIES]);

        // end data validation block

        // check if product exists
        $productModel = $this->findProduct($dataArray[SupplierProductDataInterface::SKU]);

        /* create a new product */
        if(null === $productModel){
            $updatingProductFlag = false;

            $productModel = $this->initializeNewProduct(
                $dataArray[SupplierProductDataInterface::TYPE_ID],
                $attributeSetId
            );

            if ( $this->supplierConfig->isSupplierCanDefineProductSkuEnabled() ) {
                $sku = $dataArray[SupplierProductDataInterface::SKU];
            } else {
                $sku = $this->cmDataHelper->generateSku();
            }

            $productModel->setSku($sku);
        }

        /* set categories */
        $productModel->setCategoryIds($dataArray[SupplierProductDataInterface::CATEGORIES]);

        $attributesToSkip = [
            SupplierProductDataInterface::TYPE_ID,
            SupplierProductDataInterface::SKU,
            SupplierProductDataInterface::ATTRIBUTE_SET_ID,
            SupplierProductDataInterface::CUSTOM_ATTRIBUTES,
            SupplierProductDataInterface::CATEGORIES,
            SupplierProductDataInterface::QTY,
            SupplierProductDataInterface::MEDIA_GALLERY,
            SupplierProductDataInterface::VARIATION_ATTRIBUTES
        ];

        /* process all other product data */
        $dataChangesFlag = false;
        foreach($dataArray as $attributeCode => $attributeValue){
            if(in_array($attributeCode,$attributesToSkip )) continue;
            $productModel->setData($attributeCode,$attributeValue);
            $dataChangesFlag = true;
        }

        // if we are updating product and data was changed
        if($updatingProductFlag && $dataChangesFlag){
            $productModel->setHasDataChanges(true);
        }

        //save product
        $productModel = $this->productRepository->save($productModel);
        // we should process images and qty data after product was created


        /* process images */
        // last image will be set as default
        // replacing image functionality not implemented
        if(
            $this->supplierConfig->isSupplierProductsImageUploadEnabled()
            && isset($dataArray[SupplierProductDataInterface::MEDIA_GALLERY])
            && is_array($dataArray[SupplierProductDataInterface::MEDIA_GALLERY])
        ){
            $this->productImageImport->addImagesToProduct(
                $productModel,
                $dataArray[SupplierProductDataInterface::MEDIA_GALLERY]
            );
        }

        // add configuration attributes, if they are specified
        if(Configurable::TYPE_CODE === $productModel->getTypeId()){
            if(isset($dataArray[SupplierProductDataInterface::VARIATION_ATTRIBUTES])
                && is_array($dataArray[SupplierProductDataInterface::VARIATION_ATTRIBUTES])
            ){
                $this->configProductBuilder->fillProductWithConfigurableAttributes(
                    $productModel,
                    $dataArray[SupplierProductDataInterface::VARIATION_ATTRIBUTES]
                );
            }
        }

        //save product in order to save images
        $productModel = $this->productRepository->save($productModel);

        /* process qty */
        // qty should be processed after product save, because empty qty product attribute overrides all with null
        if(Configurable::TYPE_CODE != $productModel->getTypeId()){
            $this->setProductQty($productModel, $dataArray[SupplierProductDataInterface::QTY]);
        }

        return $productModel;
    }


    /**
     * @param $productModel
     * @param $qty
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function setProductQty( $productModel, $qty){
        $stockItem = $this->stockRegistry->getStockItem($productModel->getId(), $this->getCustomerWebsiteId());
        $stockItem->setQty($qty);
        $stockItem->setIsInStock((bool)$qty);
        $this->stockRegistry->updateStockItemBySku($productModel->getSku(), $stockItem);
    }

    /**
     * @param $productType
     * @param $attributeSetId
     * @return \Magento\Catalog\Model\Product
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function initializeNewProduct($productType, $attributeSetId){
        $productModel = $this->productFactory->create();
        $productModel->setTypeId($productType);
        $productModel->setWebsiteIds(
            [$this->getCustomerWebsiteId()]
        );

        $productModel->setAttributeSetId($attributeSetId);
        $productModel->setStatus(
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        );
        $productModel->setVisibility(
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE
        );
        $productModel->setTaxClassId(
            $this->supplierConfig->getSupplierProductsTaxClass()
        );

        $productModel->setData(
            'creator_id',
            $this->getCustomerId()
        );

        // auto approval check
        if ( $this->supplierConfig
            ->isSupplierProductsAutoApprovalEnabled()
        ) {
            $productModel
                ->setSupplierActivedProduct(1)
                ->setVisibility(
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
                )->setStockData(['is_in_stock' => 1]);

            $cmProductStatus = \Cminds\Supplierfrontendproductuploader\Model\Product::STATUS_APPROVED;
        } else {
            $cmProductStatus = \Cminds\Supplierfrontendproductuploader\Model\Product::STATUS_PENDING;
        }

        $productModel->setData(
            'frontendproduct_product_status', $cmProductStatus
        );

        return $productModel;
    }

    /**
     * @param array $products
     * @return \Magento\Catalog\Model\ResourceModel\Product[]
     */
    protected function getExistingProducts(array $products){
        $productSkuArray = [];
        foreach($products as $productData){
            $productSkuArray[] = $productData->getSku();
        }
        if(count($productSkuArray)){
            $productCollection = $this->productCollectionFactory->create();
            $productCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('sku', array('in' => $productSkuArray))
                // we need to know if product exists but customer doesn't have sufficient rights to edit it
                // moved to generate an appropriate error message
//                ->addAttributeToFilter('creator_id', array('eq' => $this->currentCustomerId))
            ;

            foreach($productCollection as $product){
                $this->productCollection[$product->getSku()] = $product;
            }
        }
        return $this->productCollection;
    }


    /**
     * @return int|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCustomerWebsiteId(){
        if(null === $this->currentWebsiteId){
            $this->currentWebsiteId = $this->getCustomer()->getWebsiteId();
        }
        return $this->currentWebsiteId;
    }

    /**
     * @return int|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCustomerStoreId(){
        if(null === $this->currentStoreId){
            $this->currentStoreId = $this->getCustomer()->getStoreId();
        }
        return $this->currentStoreId;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCustomer(){
        if(null === $this->customer){
            $this->customer = $this->customerRepository->getById($this->getCustomerId());
        }
        return $this->customer;
    }


    /**
     * @param array $categoryIds
     * @throws LocalizedException
     */
    protected function validateProductCategories( array $categoryIds){
        $allowedCategories = $this->getAllowedCategories();

        $diff = array_diff($categoryIds, array_keys($allowedCategories));
        if (!empty($diff)) {
            throw new LocalizedException(
                __('Some category ids are not allowed: "%1".', implode(', ', $diff))
            );
        }
    }

    /*validation connected block*/

    /**
     * Map additional attributes
     *
     * @param $attributeSetId
     * @return mixed
     * @throws NoSuchEntityException
     */
    protected function mapSupplierAttributesBySet($attributeSetId){
        $supplierAttributes = $this->getSupplierAttributesBySet($attributeSetId);

        if( !isset($this->supplierAttributesBySetId[$attributeSetId]) ){
            $this->supplierAttributesBySetId[$attributeSetId] = array(
                'attributes' => array(),
                'required' => array()
            );

            if( $supplierAttributes->getTotalCount() > 0 ){
                foreach ($supplierAttributes->getItems() as $attribute){
                    $currentAttrCode = $attribute->getAttributeCode();

                    if( !is_array($this->supplierAttributesBySetId[$attributeSetId]['attributes'])
                        || !in_array($currentAttrCode, $this->supplierAttributesBySetId[$attributeSetId]['attributes']) ){

                        $this->supplierAttributesBySetId[$attributeSetId]['attributes'][] = $currentAttrCode;
                        if($attribute->getIsRequired()){
                            $this->supplierAttributesBySetId[$attributeSetId]['required'][] = $currentAttrCode;
                        }
                    }
                    if(!isset($this->supplierAttributes[$currentAttrCode])){
                        $this->supplierAttributes[$currentAttrCode] = [
                            'code' => $currentAttrCode,
                            'id' => $attribute->getAttributeId(),
                            'required' => $attribute->getIsRequired(),
                            'input' => $attribute->getFrontendInput(),
                        ];

                        $options = $attribute->getSource()->getAllOptions(false);
                        if(count($options)){
                            foreach ( $options as $option) {
                                $this->supplierAttributes[$currentAttrCode]['options'][] = $option['value'];
                            }
                        }

                    }
                }
            }
        }
        return $this->supplierAttributesBySetId[$attributeSetId];
    }

    /**
     * @param array $data
     * @param $attributeMapBySetId
     * @param $attributeMap
     * @return bool
     * @throws LocalizedException
     */
    protected function validateAttributeValues(array $data, $attributeMapBySetId, $attributeMap)
    {
        $requiredAttributeKeys = $this->getRequiredKeys($attributeMapBySetId);
        foreach($data as $attributeKey => $attributeValue){
            if(is_array($attributeValue)) continue;
            $attributeValue = trim($attributeValue);
            if( in_array($attributeKey, $requiredAttributeKeys)
                && $attributeValue == ''
                // validate product type
                || $attributeKey === SupplierProductDataInterface::TYPE_ID
                && !in_array($attributeValue, $this->getAllowedAttributeTypes())
            ){
                throw new LocalizedException(
                    __('Required attribute "%1" is empty.', $attributeKey)
                );
            }

            if(isset($attributeMap[$attributeKey])){
                if( $attributeMap[$attributeKey]['input'] === 'decimal'
                    && $attributeValue != ''
                    && !is_numeric($attributeValue)
                ){
                    throw new LocalizedException(
                        __('"%1" value is invalid.', $attributeKey)
                    );
                }

                if( $attributeMap[$attributeKey]['input'] === 'select'
                    && $attributeValue != ''
                    && !in_array($attributeValue, $attributeMap[$attributeKey]['options'])
                ){
                    throw new LocalizedException(
                        __('"%1" value is invalid.', $attributeKey)
                    );
                }
            }
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getAllowedAttributeTypes(){
        return [
            Type::TYPE_SIMPLE,
            Configurable::TYPE_CODE
        ];
    }

    /**
     * Filter provided data, remove not allowed keys and return filtered array.
     *
     * @param array $data
     * @param array $attributeMap
     *
     * @return array
     */
    protected function filterData(array $data, array $attributeMap)
    {
        $allowedKeys = $this->getKeys($attributeMap);

        foreach ($data as $key => $value) {
            if (
                in_array($key, $allowedKeys, true)
                && is_null($value) === false
            ) {
                continue;
            }

            unset($data[$key]);
        }

        return $data;
    }

    /**
     * Check if all required keys exist.
     *
     * @param array $keys
     * @param array $attributeMap
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function validateKeys(array $keys, array $attributeMap)
    {
        $diff = array_diff($this->getRequiredKeys($attributeMap), $keys);
        if (!empty($diff)) {
            throw new LocalizedException(
                __("Required attributes are missing: " . implode(', ', $diff) )
            );
        }
        return $this;
    }

    /**
     * Return field keys which are available in import.
     *
     * @param array $attributeMap
     * @return array
     */
    protected function getKeys(array $attributeMap)
    {
        return array_unique(
            array_merge(
                $this->getBaseRequiredKeys(),
                $this->getBaseOptionalKeys(),
                $attributeMap['attributes']
            )
        );
    }

    /**
     * Return field keys which are required in import.
     *
     * @param array $attributeMap
     * @return array
     */
    protected function getRequiredKeys(array $attributeMap)
    {
        return array_unique(
            array_merge(
                $this->getBaseRequiredKeys(),
                $attributeMap['required']
            )
        );
    }

    /**
     * Get required attribute codes
     * @return array
     */
    protected function getBaseRequiredKeys(){
        return SupplierProductDataInterface::MANDATORY_ATTRIBUTES;
    }

    /**
     * Get optional attribute codes
     * @return array
     */
    protected function getBaseOptionalKeys(){
        return SupplierProductDataInterface::OPTIONAL_ATTRIBUTES;
    }

    /*end validation block*/

    /**
     * @return int
     */
    protected function getCustomerId(){
        return $this->currentCustomerId;
    }

    /**
     * @param string $dataKey
     * @param string $errorMessage
     * @return string
     */
    protected function addKeyToErrorMessage($dataKey, $errorMessage){
        return __('Product key: %1', $dataKey) . ' : ' . $errorMessage;
    }

    /**
     * @param $productSku
     * @return \Magento\Catalog\Model\ResourceModel\Product|null
     * @throws LocalizedException
     */
    protected function findProduct($productSku){
        $result = null;
        if(isset($this->productCollection[$productSku])){
            $product = $this->productCollection[$productSku];
            if($this->canUpdateProduct($product)){
                $result = $product;
            } else {
                throw new LocalizedException(
                    __(
                        'You do not have permission to update this product. SKU: "%1"'
                        , $product->getSku()
                    )
                );
            }
        }
        return $result;
    }

    /**
     * @param $product
     * @return bool
     */
    protected function canUpdateProduct($product){
        return $product->getData('creator_id') == $this->getCustomerId();
    }

    /**
     * Get allowed category IDs and names
     *
     * @return array
     */
    protected function getAllowedCategories(){

        if( null === $this->supplierAllowedCategoryIds ){
            // even if no categories are found, we should not call this method again
            $this->supplierAllowedCategoryIds = [];

            $allowedCategories = $this->getSupplierAllowedCategories();

            if($allowedCategories->getTotalCount() > 0){
                foreach($allowedCategories->getItems() as $category){
                    if(!isset($this->supplierAllowedCategoryIds[$category->getId()])){
                        $this->supplierAllowedCategoryIds[$category->getId()] = $category->getName();
                    }
                }
            }
        }
        return $this->supplierAllowedCategoryIds;
    }

    /**
     * Get all allowed categories
     *
     * @return \Magento\Catalog\Api\Data\CategorySearchResultsInterface
     */
    protected function getSupplierAllowedCategories(){
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('available_for_supplier',1)
            ->create();
        // @toDo: check if we need to remove some fields from the response data
        return $this->categoryList->getList($searchCriteria);
    }

    /**
     * Check token
     *
     * @param string $userAccessToken
     * @return int|bool
     */
    public function checkToken($userAccessToken){
        $apiToken = $this->apiTokenFactory->create();
        return $apiToken->getCustomerIdByToken($userAccessToken);
    }

    /**
     * @param $userAccessToken
     * @return bool|int
     * @throws NoSuchEntityException
     */
    public function canAccess($userAccessToken){

        if(false === $this->supplierConfig->isEnabled()){
            throw new \Exception(
                __('Functionality Disabled')
            );
        }

        if(false === $customerId = $this->checkToken($userAccessToken)){
            throw new NoSuchEntityException(
                __('Invalid Token')
            );
        }
        $this->currentCustomerId = $customerId;

        return $this->currentCustomerId;
    }

}
