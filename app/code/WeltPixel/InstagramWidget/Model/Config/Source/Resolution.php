<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class Resolution implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'thumbnail', 'label' => '150x150'],
            ['value' => 'low_resolution', 'label' => '306x306'],
            ['value' => 'standard_resolution', 'label' => '612x612']
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
            'thumbnail' => '150x150',
            'low_resolution' => '306x306',
            'standard_resolution' => '612x612'
        ];
    }
}
