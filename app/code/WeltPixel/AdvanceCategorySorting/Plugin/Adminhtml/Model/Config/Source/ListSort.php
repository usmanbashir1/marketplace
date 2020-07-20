<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Adminhtml\Model\Config\Source;

class ListSort
{
    /**
     * @var \WeltPixel\AdvanceCategorySorting\Helper\Data
     */
    protected $_helper;

    /**
     * ListSort constructor.
     * @param \WeltPixel\AdvanceCategorySorting\Helper\Data $helper
     */
    public function __construct(\WeltPixel\AdvanceCategorySorting\Helper\Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Add new options to catalog/frontend/default_sort_by config
     *
     * @param \Magento\Catalog\Model\Config\Source\ListSort $subject
     * @param $result
     * @return array
     */
    public function afterToOptionArray(\Magento\Catalog\Model\Config\Source\ListSort $subject, $result)
    {
        if ($this->_helper->getConfigValue('general', 'enable')) {
            $options = $this->_helper->getAllConfigValues('general', $this->_helper->getStoreId());

            $removed = [];
            $newOptions = [];
            foreach ($options as $code => $data) {
                if ($data['enable']) {
                    $key = $this->_helper->getSortOrder($data, $newOptions);
                    $newOptions[$key] = [
                        'label' => __($data['name']),
                        'value' => $code
                    ];
                } else {
                    $removed[] = $code;
                }
            }

            /**
             * sort $newOptions by key
             * remove duplicated and disabled options
             */
            ksort($newOptions);
            foreach ($newOptions as $option) {
                foreach ($result as $key => $values) {
                    if ($values['value'] == $option['value'] || in_array($values['value'], $removed)) {
                        unset($result[$key]);
                        break;
                    }
                }
            }

            return array_merge($newOptions, $result);
        }

        return $result;
    }
}