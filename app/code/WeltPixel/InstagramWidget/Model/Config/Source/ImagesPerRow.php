<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class ImagesPerRow implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'col-2', 'label' => '2'],
            ['value' => 'col-3', 'label' => '3'],
            ['value' => 'col-4', 'label' => '4'],
            ['value' => 'col-5', 'label' => '5'],
            ['value' => 'col-6', 'label' => '6']
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
            'col-2' => '2',
            'col-3' => '3',
            'col-4' => '4',
            'col-5' => '5',
            'col-6' => '6'
        ];
    }
}
