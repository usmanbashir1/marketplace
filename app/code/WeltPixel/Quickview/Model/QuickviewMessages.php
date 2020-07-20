<?php
namespace WeltPixel\Quickview\Model;

/**
 * Class QuickviewMessages
 * @package WeltPixel\Quickview\Model
 */
class QuickviewMessages extends \Magento\Rule\Model\AbstractModel
{
    const CACHE_TAG = 'weltixel_quickviewmessages';

    /**
     * @var string
     */
    protected $_cacheTag = 'weltixel_quickviewmessages';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'weltixel_quickviewmessages';

    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface $_storeRepository
     */
    protected $_storeRepository;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $_resourceIterator;

    /**
     * AbstractModel constructor
     *
     * @param \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_combineFactory = $combineFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_storeRepository = $storeRepository;
        $this->_resourceIterator = $resourceIterator;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\Quickview\Model\ResourceModel\QuickviewMessages');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return \Magento\Rule\Model\Action\Collection | null
     */
    public function getActionsInstance()
    {
        return null;
    }

    /**
     * Reset rule actions
     *
     * @param null|\Magento\Rule\Model\Action\Collection $actions
     * @return $this
     */
    protected function _resetActions($actions = null)
    {
        return $this;
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if ($this->_productIds === null) {
            $this->_productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->_productCollectionFactory->create();
            $productCollection->addWebsiteFilter($this->getWebsiteIds());

            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->_resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->_productFactory->create()
                ]
            );
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        $websiteStores = $this->_getWebsitesStoresMap();
        $results = [];

        foreach ($websiteStores as $storeId) {
            $product->setStoreId($storeId);
            if ($this->getConditions()->validate($product)) {
                $results[$storeId] = $product->getId();
            }
        }
        if (!empty($results)) {
            $this->_productIds[] = $results;
        }
    }

    /**
     * Prepare website map
     *
     * @return array
     */
    protected function _getWebsitesMap()
    {
        $map = [];
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            // Continue if website has no store to be able to create catalog rule for website without store
            if ($website->getDefaultStore() === null) {
                continue;
            }
            $map[$website->getId()] = $website->getDefaultStore()->getId();
        }
        return $map;
    }

    /**
     * @return array
     */
    protected function _getStoreIdsMap()
    {
        $map = [];
        $stores = $this->_storeRepository->getList();
        foreach ($stores as $store) {
            if ($store->getId()) {
                $map[$store->getId()] = $store->getWebsiteId();
            }
        }
        return $map;
    }

    /**
     * @return mixed
     */
    protected function _getWebsitesStoresMap()
    {
        if (!$this->hasWebsitesStoresIds()) {
            $storesMap = $this->_getStoreIdsMap();
            $storeIds = explode(",", $this->getData('store_id'));
            if (in_array(0, $storeIds)) {
                $websiteStoreIds = array_keys($storesMap);
                $this->setData('websites_stores_ids', $websiteStoreIds);
            } else {
                $websiteStoreIds = [];
                foreach($storeIds as $id) {
                    $websiteStoreIds[] = $id;
                }
                $this->setData('websites_stores_ids', $websiteStoreIds);
            }
        }
        return $this->_getData('websites_stores_ids');
    }

    /**
     * @return array
     */
    public function getWebsiteIds()
    {
        if (!$this->hasWebsiteIds()) {
            $storesMap = $this->_getStoreIdsMap();
            $storeIds = explode(",", $this->getData('store_id'));
            if (in_array(0, $storeIds)) {
                $websiteIds = array_unique($storesMap);
                $this->setData('website_ids', $websiteIds);
            } else {
                $websiteIds = [];
                foreach($storeIds as $id) {
                    $websiteIds[] = $storesMap[$id];
                }
                $websiteIds = array_unique($websiteIds);
                $this->setData('website_ids', $websiteIds);
            }
        }
        return $this->_getData('website_ids');
    }

    /**
     * @return mixed
     */
    public function getAllStoreIdsAssigned()
    {
        return $this->_getWebsitesStoresMap();
    }
}
