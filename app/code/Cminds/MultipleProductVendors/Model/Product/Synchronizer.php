<?php

namespace Cminds\MultipleProductVendors\Model\Product;

use Cminds\MultipleProductVendors\Model\ManufacturerFactory;
use Cminds\MultipleProductVendors\Model\Product as ProductHelper;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Processor as GalleryProcessor;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProdutCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Synchronizer
{
    const PROCESSING_VENDOR_CANDIDATES = 'processing_vendor_candidates';

    /**
     * Product factory.
     *
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * Class, which contains additional useful methods for the product.
     *
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * Product Collection factory.
     *
     * @var ProdutCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Core registry.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Filesystem.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Gallery processor.
     *
     * @var GalleryProcessor
     */
    private $galleryProcessor;

    /**
     * Manufacturer code entity.
     *
     * @var ManufacturerFactory
     */
    private $manufacturerFactory;

    /**
     * Category link management.
     *
     * @var CategoryLinkManagementInterface
     */
    private $categoryLinkManagement;

    /**
     * Option factory.
     *
     * @var OptionFactory
     */
    private $optionFactory;

    /**
     * Product link.
     *
     * @var ProductLinkInterfaceFactory
     */
    private $productLink;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var Product
     */
    private $productPrototype;

    /**
     * @var array
     */
    private $candidates;

    /**
     * @var string
     */
    private $manufacturerCode;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Synchronizer constructor.
     *
     * @param ProductFactory                  $productFactory
     * @param ProdutCollectionFactory         $productCollectionFactory
     * @param Registry                        $registry
     * @param Filesystem                      $filesystem
     * @param GalleryProcessor                $galleryProcessor
     * @param ManufacturerFactory             $manufacturerFactory
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param OptionFactory                   $optionFactory
     * @param ProductLinkInterfaceFactory     $productLinkInterface
     * @param AttributeFactory                $attributeFactory
     * @param ProductHelper                   $productHelper
     * @param DataObjectFactory               $dataObjectFactory
     * @param ProductRepositoryInterface      $productRepository
     */
    public function __construct(
        ProductFactory $productFactory,
        ProdutCollectionFactory $productCollectionFactory,
        Registry $registry,
        Filesystem $filesystem,
        GalleryProcessor $galleryProcessor,
        ManufacturerFactory $manufacturerFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        OptionFactory $optionFactory,
        ProductLinkInterfaceFactory $productLinkInterface,
        AttributeFactory $attributeFactory,
        ProductHelper $productHelper,
        DataObjectFactory $dataObjectFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->registry = $registry;
        $this->filesystem = $filesystem;
        $this->galleryProcessor = $galleryProcessor;
        $this->manufacturerFactory = $manufacturerFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->optionFactory = $optionFactory;
        $this->productLink = $productLinkInterface;
        $this->attributeFactory = $attributeFactory;
        $this->productHelper = $productHelper;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $manufacturerCode
     *
     * @return Synchronizer
     */
    public function setManufacturerCode($manufacturerCode)
    {
        $this->manufacturerCode = $manufacturerCode;
        $this->candidates = null; // reset candidates collection

        return $this;
    }

    /**
     * Find the cheapest product among manufacturers and replace data from main
     * product.
     *
     * We take id of the product. After that the manufacturer code is retrieved
     * and the cheapest product is found and synchronized with main product.
     *
     * @return Product|null
     * @throws \Exception
     */
    public function findCheapestProduct()
    {
        if ($this->manufacturerCode === null) {
            throw new LocalizedException(__('Manufacturer code has been not set.'));
        }

        $this->registry->register(self::PROCESSING_VENDOR_CANDIDATES, true);

        try {
            /** @var Product $mainProduct */
            $mainProduct = $this->getMainProduct();
            if ($mainProduct->getId() === null) {
                throw new LocalizedException(
                    __(
                        'Main product not found for "%1" manufacturer code.',
                        $this->manufacturerCode
                    )
                );
            }

            $candidates = $this->getCandidates();
            if ($candidates->getSize() === 0) {
                $this->manufacturerFactory->create()
                    ->load($this->manufacturerCode, 'manufacturer_code')
                    ->delete();

                throw new LocalizedException(
                    __(
                        'No vendor candidates found for "%1" manufacturer code.',
                        $this->manufacturerCode
                    )
                );
            }

            /**
             * Make sure that best candidate will be visible.
             */
            $cheapestCandidate = $this->getCheapestCandidate();

            if ($cheapestCandidate->getId() === null) {
                throw new LocalizedException(__('Candidates has been not found.'));
            }

            $this->registry->unregister('cheapest_candidate_id');
            $this->registry->register(
                'cheapest_candidate_id',
                $cheapestCandidate->getId()
            );

            $this->syncData($mainProduct, $cheapestCandidate);

            $this->registry->unregister(self::PROCESSING_VENDOR_CANDIDATES);
        } catch (\Exception $e) {
            $this->registry->unregister(self::PROCESSING_VENDOR_CANDIDATES);

            return null;
        }

        return $cheapestCandidate;
    }

    /**
     * @return Product
     */
    private function getProductPrototype()
    {
        if ($this->productPrototype === null) {
            $this->productPrototype = $this->productFactory->create()
                ->setStoreId(Store::DEFAULT_STORE_ID);
        }

        return clone $this->productPrototype;
    }

    /**
     * @return int
     */
    private function getMainAttrId()
    {
        return  $this->attributeFactory->create()
            ->getIdByCode(
                Product::ENTITY,
                'main_product'
            );
    }

    /**
     * @return Collection
     */
    private function getCandidates()
    {
        if ($this->candidates === null) {
            /** @var Collection $candidates */
            $candidates = $this->productCollectionFactory->create()
                ->addFieldToFilter('manufacturer_code', $this->manufacturerCode);

            $candidates->getSelect()
                ->joinLeft(
                    ['at_main_product' => $candidates->getTable('catalog_product_entity_int')],
                    'at_main_product.entity_id = e.entity_id'
                    . ' AND at_main_product.attribute_id = ' . $this->getMainAttrId()
                    . ' AND at_main_product.store_id = 0',
                    ['main_product' => 'at_main_product.value']
                )
                ->where('(at_main_product.value is null')
                ->orWhere(
                    'at_main_product.value = ?)',
                    ProductHelper::PRODUCT_IS_NOT_MAIN
                );

            $this->candidates = $candidates;
        }

        return $this->candidates;
    }

    /**
     * @return Product|DataObject
     */
    private function getCheapestCandidate()
    {
        $candidates = $this->getCandidates();
        $cheapestCandidate = $this->dataObjectFactory->create();

        foreach ($candidates as $candidate) {
            /** @var Product $candidate */
            $candidate = $this->getProductPrototype()
                ->load($candidate->getId());

            if ($candidate->isSalable() === false) {
                continue;
            }

            if ($cheapestCandidate->getId() === null) {
                $cheapestCandidate = $candidate;
                continue;
            }

            $cheapestPrice = $cheapestCandidate->getPrice();
            if ($cheapestCandidate->getSpecialPrice()) {
                $cheapestPrice = $cheapestCandidate->getSpecialPrice();
            }

            $currentPrice = $candidate->getPrice();
            if ($candidate->getSpecialPrice()) {
                $currentPrice = $candidate->getSpecialPrice();
            }

            if ($currentPrice < $cheapestPrice) {
                $cheapestCandidate = $candidate;
            }
        }

        return $cheapestCandidate;
    }

    /**
     * @return Product|DataObject
     */
    private function getMainProduct()
    {
        $mainProduct = $this->productCollectionFactory->create()
            ->addFieldToFilter('manufacturer_code', $this->manufacturerCode)
            ->addFieldToFilter('main_product', ProductHelper::PRODUCT_IS_MAIN)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();

        return $mainProduct;
    }

    /**
     * Synchronize data between cheapest manufacturer and main product.
     *
     * @param Product $source
     * @param Product $candidate
     *
     * @return Product
     * @throws \Exception
     */
    private function syncData(Product $source, Product $candidate)
    {
        // update product source special price with the lowest price
        $source->setSpecialPrice($candidate->getPrice());
        $this->productRepository->save($source);

        return $source;
    }
}
