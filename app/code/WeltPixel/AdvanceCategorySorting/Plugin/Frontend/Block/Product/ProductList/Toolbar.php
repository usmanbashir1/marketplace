<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Frontend\Block\Product\ProductList;

use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use WeltPixel\AdvanceCategorySorting\Helper\Data as Helper;

class Toolbar
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var ToolbarModel
     */
    protected $_toolbarModel;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var bool - flag for already filtered collection
     */
    protected $filtered = false;

    /**
     * Toolbar constructor.
     * @param ToolbarModel $toolbarModel
     * @param StoreManager $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     * @param Helper $helper
     */
    public function __construct(
        ToolbarModel $toolbarModel,
        StoreManager $storeManager,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        Helper $helper
    )
    {
        $this->_toolbarModel = $toolbarModel;
        $this->_storeManager  = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_registry = $registry;
        $this->_helper  = $helper;
    }

    /**
     * Filter products collection
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param $result
     * @return mixed
     */
    public function afterSetCollection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $result)
    {
        if ($this->_helper->getConfigValue('general', 'enable')) {
            $collection = $subject->getCollection();

            if ($subject->getCurrentOrder()) {
                if ($this->_getRealCurrentOrder($subject) == 'position') {
                    if (!$this->filtered) {
                        $collection->addAttributeToSort(
                            $this->_getRealCurrentOrder($subject),
                            $this->_getRealCurrentDirection($subject)
                        )->addAttributeToSort('entity_id', $this->_getRealCurrentDirection($subject));
                    }
                    $this->filtered = true;
                } else {
                    switch ($this->_getRealCurrentOrder($subject)) {
                        // filter collection by given custom attribute
                        case 'new_arrivals':
                            if (!$this->filtered) {
                                $collection->setOrder('created_at', $this->_getRealCurrentDirection($subject));
                                $collection->setOrder('entity_id', 'ASC');
                            }
                            $this->filtered = true;

                            break;

                        case 'top_seller':
                            if (!$this->filtered) {
                                $joinTableName = $collection->getTable('sales_order_item');
                                $collection->getSize();
                                $collection->getSelect()->joinLeft(
                                        $joinTableName,
                                        'e.entity_id = ' . $joinTableName . '.product_id AND ' . $joinTableName . '.store_id=' . $this->_storeManager->getStore()->getId(),
                                        [$joinTableName . '.qty_ordered' => 'SUM(' . $joinTableName . '.qty_ordered) AS ordered']
                                    )
                                    ->group('e.entity_id')
                                    ->order('ordered ' . $this->_getRealCurrentDirection($subject))
                                    ->order('entity_id ASC')
                                ;
                            }
                            $this->filtered = true;

                            break;

                        case 'top_rated':
                            if (!$this->filtered) {
                                $joinTableName = $collection->getTable('rating_option_vote_aggregated');
                                $collection->getSelect()->joinLeft(
                                    $joinTableName,
                                    'e.entity_id = ' . $joinTableName . '.entity_pk_value AND ' . $joinTableName . '.store_id=' . $this->_storeManager->getStore()->getId(),
                                    [$joinTableName . '.percent_approved']
                                )
                                ->order('percent_approved ' . $this->_getRealCurrentDirection($subject))
                                ->order('entity_id ASC')
                                ;

                            }
                            $this->filtered = true;

                            break;

                        case 'most_reviewed':
                            if (!$this->filtered) {$joinTableName = $collection->getTable('review_entity_summary');
                                $collection->getSelect()->joinLeft(
                                    $joinTableName,
                                    'e.entity_id = ' . $joinTableName . '.entity_pk_value AND ' . $joinTableName . '.store_id=' . $this->_storeManager->getStore()->getId(),
                                    [$joinTableName . '.reviews_count']
                                )
                                ->order('reviews_count ' . $this->_getRealCurrentDirection($subject))
                                ->order('entity_id ASC')
                                ;
                            }
                            $this->filtered = true;
                            break;
                        case 'relevance':
                            if (!$this->filtered) {
                                $collection->setOrder($this->_getRealCurrentOrder($subject), 'desc');
                            }
                            $this->filtered = true;
                            break;

                        default:
                            if (!$this->filtered) {
                                $collection->setOrder($this->_getRealCurrentOrder($subject), $this->_getRealCurrentDirection($subject));
                            }
                            $collection->setOrder('entity_id', 'ASC');
                            $this->filtered = true;

                            break;
                    }
                }
            }
        }

        return $subject;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $proceed
     * @param $field
     * @return mixed
     */
    public function aroundSetDefaultOrder(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $field
    )
    {
        if ($this->_getRealCurrentOrder($subject) == 'relevance') {
            $field = 'relevance';
        } else {
            $defaultSortOrder = $this->_getDefaultSortOrder();
            $currentSortOrder = $this->_toolbarModel->getOrder();
            $field = $currentSortOrder ? $currentSortOrder : $defaultSortOrder;
        }
        /**
         * concat direction if missing but not for 'relevance'
         */
        if (strpos($field, '~') === false && $field != 'relevance') {
            $direction = $this->_getRealCurrentDirection($subject);
            $field .=  '~' . $direction;
        }

        return $proceed($field);
    }

    /**
     * Split param by '~' sign and return the real attribute name
     *
     * @param $subject
     * @return mixed
     */
    private function _getRealCurrentOrder($subject)
    {
        $orders = $subject->getAvailableOrders();
        $defaultOrder = $subject->getOrderField();

        if (!isset($orders[$defaultOrder])) {
            $defaultOrder = isset($orders['relevance']) ? 'relevance' : $this->_getDefaultSortOrder();
        }

        $order = $this->_toolbarModel->getOrder();
        if (!$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }

        $orderArr = explode('~', $order);

        return reset($orderArr);
    }

    /**
     * Try to get direction from url param else return a default value
     *
     * @param $subject
     * @return bool|string
     */
    private function _getRealCurrentDirection($subject)
    {
        if ($direction = $this->_toolbarModel->getDirection()) {
            return $direction;
        }

        switch ($this->_getRealCurrentOrder($subject)) {
            case 'new_arrivals':
            case 'top_seller':
            case 'top_rated':
            case 'most_reviewed':
            case 'relevance':
                $direction = 'desc';
                break;
            default:
                $direction = $subject->getCurrentDirection();
                break;
        }

        return $direction;
    }

    /**
     * Get default_sort_by attribute
     *
     * @return mixed
     */
    private function _getDefaultSortOrder()
    {
        $currentCategory = $this->_registry->registry('current_category');
        if ($currentCategory && $defaultCategorySortBy = $currentCategory->getData('default_sort_by')) {
            return $defaultCategorySortBy;
        }

        return $this->_scopeConfig->getValue(
            'catalog/frontend/default_sort_by',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }

    /**
     * Set "relevance" as default sort order for catalog search result page
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param $result
     * @return string
     */
    public function afterGetCurrentOrder(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $result)
    {
        $orders = $subject->getAvailableOrders();

        // compare the value set in param with order returned in $result
        if ($result == $this->_toolbarModel->getOrder()) {
            return $result;
        }

        if (isset($orders['relevance'])) {
            // current page should be catalogsearch/result page
            return 'relevance';
        }

        // if nothing changed...
        return $result;
    }
}