<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class ImageSize implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '150', 'label' => '150x150'],
            ['value' => '240', 'label' => '240x240'],
            ['value' => '320', 'label' => '320x320'],
            ['value' => '480', 'label' => '480x480'],
            ['value' => '640', 'label' => '640x640'],
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
            '150' => '150x150',
            '240' => '240x240',
            '320' => '320x320',
            '480' => '480x480',
            '640' => '640x640',
        ];
    }
}
