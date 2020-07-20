<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class ApiType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'javascript_parser', 'label' => __('Javascript Fetching')],
            ['value' => 'old', 'label' => __('Old Api (Deprecated)')]
        ];
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return [
            'javascript_parser' => __('Javascript Fetching'),
            'old' => __('Old Api (Deprecated)')
        ];
    }
}
