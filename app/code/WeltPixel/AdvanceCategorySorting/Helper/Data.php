<?php

namespace WeltPixel\AdvanceCategorySorting\Helper;

/**
 * Class Data
 * @package WeltPixel\AdvanceCategorySorting\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get config value
     *
     * @param $group
     * @param $field
     * @param null $storeId
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getConfigValue($group, $field, $storeId = null)
    {
        if (!$storeId)
            $storeId = $this->getStoreId();

        return $this->scopeConfig->getValue(
            'weltpixel_advance_category_sorting/' . $group . '/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get config values by group  as array
     *
     * @param $group
     * @param null $storeId
     * @return array
     */
    public function getConfigValuesByGroup($group, $storeId = null)
    {
        if (!$storeId)
            $storeId = $this->getStoreId();

        switch ($group) {
            case 'general':
                $fields = [
                    'enable',
                ];
                break;
            default:
                $fields = [
                    'enable',
                    'name',
                    'both_direction',
                    'sort_order'
                ];
        }

        $configValues = [];
        foreach ($fields as $field) {
            $configValues[$field] = $this->getConfigValue($group, $field, $storeId);
        }

        return $configValues;
    }

    /**
     * Get all config values as array
     *
     * @param null $excludeGroup
     * @param null $storeId
     * @return array
     */
    public function getAllConfigValues($excludeGroup = null, $storeId = null)
    {
        if (!$storeId)
            $storeId = $this->getStoreId();

        $groups = [
            'general',
            'position',
            'name',
            'price',
            'new_arrivals',
            'top_seller',
            'top_rated',
            'most_reviewed',
        ];

        if ($excludeGroup) {
            if (($key = array_search($excludeGroup, $groups)) !== false) {
                unset($groups[$key]);
            }
        }

        $configValues = [];
        foreach ($groups as $group) {
            $configValues[$group] = $this->getConfigValuesByGroup($group, $storeId);
        }

        return $configValues;
    }

    /**
     * Make sure sort_order is not duplicated
     *
     * @param $data
     * @param $newOptions
     * @return mixed
     */
    public function getSortOrder($data, $newOptions)
    {
        $sortOrder = (int) $data['sort_order'];

        if (array_key_exists($sortOrder, $newOptions)) {
            $sortOrder = $this->_incrementSortOrder($sortOrder, $newOptions);
        }

        return $sortOrder;
    }

    /**
     * Increment existing sort order
     *
     * @param $sortOrder
     * @param $newOptions
     * @return mixed
     */
    public function _incrementSortOrder($sortOrder, $newOptions)
    {
        $sortOrder++;
        if (array_key_exists($sortOrder, $newOptions)) {
            $sortOrder = $this->_incrementSortOrder($sortOrder, $newOptions);
        }

        return (int) $sortOrder;
    }

    /**
     * Check if toolbar.js need to be extended
     *
     * @return bool
     */
    public function overwriteToolbar()
    {
        if ($this->isLnAjaxEnabled()) {
            /**
             * overwrite if category is type layered or
             * page is catalogsearch/result
             */
            $currentCategory = $this->_registry->registry('current_category');
            if (($currentCategory && $currentCategory->getIsAnchor()) || !$currentCategory) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }

    /**
     * Check if WeltPixel_LayeredNavigation module is enabled
     *
     * @return mixed
     */
    public function isLnAjaxEnabled()
    {
        if (
            $this->isModuleEnabled('WeltPixel_LayeredNavigation') &&
            $this->isOutputEnabled('WeltPixel_LayeredNavigation')
        ) {
            return $this->scopeConfig->getValue(
                'weltpixel_layerednavigation/general/ajax',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->getStoreId()
            );
        }

        return false;
    }


}