<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class YesNo implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => 'No'],
            ['value' => '1', 'label' => 'Yes']
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            '0' => 'No',
            '1' => 'Yes'
        ];
    }
}
