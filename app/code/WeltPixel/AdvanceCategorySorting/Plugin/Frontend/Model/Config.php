<?php


namespace WeltPixel\AdvanceCategorySorting\Plugin\Frontend\Model;

class Config
{
    /**
     * @var \WeltPixel\AdvanceCategorySorting\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Config constructor.
     * @param \WeltPixel\AdvanceCategorySorting\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \WeltPixel\AdvanceCategorySorting\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_helper = $helper;
        $this->_registry = $registry;
    }

    /**
     * Add new options to available_sort_by config from category edit
     *
     * @param \Magento\Catalog\Model\Config $subject
     * @param $result
     * @return array
     */
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $subject, $result)
    {
        if ($this->_helper->getConfigValue('general', 'enable')) {
            $options = $this->_helper->getAllConfigValues('general', $this->_helper->getStoreId());

            /**
             * add missing attributes to default sort options
             */
            $sortOrder = rand(10000, 99999);
            foreach ($result as $attrCode => $label) {
                if (array_key_exists($attrCode, $options)) continue;

                $options[$attrCode] = [
                    'enable' => 1,
                    'name' => $label,
                    'both_direction' => 0,
                    'sort_order' => $sortOrder
                ];
                $sortOrder++;
            }

            $removed = [];
            $newOptions = [];
            $finalOptions = [];

            /**
             * get available_sort_by attributes of current category
             * and also remove "position" if not category page
             */
            $categoryAvailableSortBy = [];
            $currentCategory = $this->_registry->registry('current_category');
            if ($currentCategory) {
                $categoryAvailableSortBy = $this->_getCategoryAvailableOrders($currentCategory);
            } else {
                if (isset($options['position']))
                    unset($options['position']);
            }

            foreach ($options as $code => $data) {
                ksort($newOptions);
                if ($data['enable']) {
                    /**
                     * remove unavailable attributes if the current category has custom available_sort_by options
                     */
                    if ($categoryAvailableSortBy && !in_array($code, $categoryAvailableSortBy)) {
                        $removed[] = $code;
                        continue;
                    }
                    $key = $this->_helper->getSortOrder($data, $newOptions);
                    if ($data['both_direction']) {
                        $bothOptions = $this->_createBothDirectionLabels($code, $data, $key);
                        // auto-increment option key if already exists
                        foreach ($bothOptions as $boKey => $val) {
                            $keyExists = array_key_exists($boKey, $newOptions);
                            if ($keyExists) {
                                $newKey = $this->_helper->_incrementSortOrder($boKey, $newOptions);
                                $currentOption = $newOptions[$boKey];
                                unset($newOptions[$boKey]);
                                $newOptions[$newKey] = $currentOption;
                            }
                        }
                        // add the new options to options arr
                        $newOptions += $bothOptions;
                    } else {
                        switch ($code) {
                            case 'new_arrivals':
                            case 'top_seller':
                            case 'top_rated':
                            case 'most_reviewed':
                            case 'relevance':
                                $direction = '~desc';
                                break;
                            default:
                                $direction = '~asc';
                                break;
                        }
                        $newOptions[$key] = [$code . $direction => __($data['name'])];
                    }
                } else {
                    $removed[] = $code;
                }
            }

            /**
             * sort $newOptions by key
             * forces the array keys to start from zero
             */
            ksort($newOptions);
            $finalOptions = array_merge($newOptions, $finalOptions);

            /**
             * remove duplicated and disabled options
             */
            for ($i = 0; $i < count($finalOptions); $i++) {
                foreach ($result as $rValue => $rLabel) {
                    $codeArr = explode('~', key($finalOptions[$i]));
                    if ($rValue == reset($codeArr) || in_array($rValue, $removed)) {
                        unset($result[$rValue]);
                        break;
                    }
                }
                // rebuild $finalOptions
                $finalOptions[key($finalOptions[$i])] = $finalOptions[$i][key($finalOptions[$i])];
                unset($finalOptions[$i]);
            }

            return array_merge($finalOptions, $result);
        }

        return $result;
    }

    /**
     * Make option available in both directions: asc and desc
     *
     * @param $code
     * @param $data
     * @param $key
     * @return array
     */
    protected function _createBothDirectionLabels($code, $data, $key)
    {
        $options = [];
        $label = $data['name'];

        switch ($code) {
            case 'name':
                $options[$key] = [$code . '~asc' => __($label . ' (A to Z)')];
                $key++; $options[$key] = [$code . '~desc' => __($label . ' (Z to A)')];
                break;
            case 'price':
                $options[$key] = [$code . '~asc' => __($label . ' (Low to High)')];
                $key++; $options[$key] = [$code . '~desc' => __($label . ' (High to Low)')];
                break;
            default:
                $options[$key] = [$code . '~desc' => __($label)];
                break;
        }

        return $options;
    }

    /**
     * Returns the custom available_sort_by options of current category if any
     *
     * @param $currentCategory
     * @return mixed
     */
    protected function _getCategoryAvailableOrders($currentCategory)
    {
        return $currentCategory->getData('available_sort_by');
    }
}