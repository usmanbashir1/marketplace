<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class AltText implements \Magento\Framework\Option\ArrayInterface
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
            ['value' => '1', 'label' => 'Original ALT from Instagram'],
            ['value' => '2', 'label' => 'Custom ALT tag']
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
            '1' => 'Original ALT from Instagram',
            '2' => 'Custom ALT tag',
        ];
    }
}
