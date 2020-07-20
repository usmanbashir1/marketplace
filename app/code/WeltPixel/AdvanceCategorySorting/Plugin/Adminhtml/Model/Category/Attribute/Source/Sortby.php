<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Adminhtml\Model\Category\Attribute\Source;

class Sortby
{
    /**
     * @var \WeltPixel\AdvanceCategorySorting\Helper\Data
     */
    protected $_helper;

    /**
     * Sortby constructor.
     * @param \WeltPixel\AdvanceCategorySorting\Helper\Data $helper
     */
    public function __construct(\WeltPixel\AdvanceCategorySorting\Helper\Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Add new options to available_sort_by config from category edit
     *
     * @param \Magento\Catalog\Model\Category\Attribute\Source\Sortby $subject
     * @param $result
     * @return array
     */
    public function afterGetAllOptions(\Magento\Catalog\Model\Category\Attribute\Source\Sortby $subject, $result)
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