<?php
namespace WeltPixel\DesignElements\Model\Config\Source;

class Brands implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'grid-6', 'label' => __('6 columns')],
            ['value' => 'grid-5', 'label' => __('5 columns')],
            ['value' => 'grid-4', 'label' => __('4 columns')],
            ['value' => 'grid-3', 'label' => __('3 columns')],
            ['value' => 'grid-2', 'label' => __('2 columns')],
            ['value' => 'carousel', 'label' => __('Carousel')]
        ];
    }
}
