<?php
namespace WeltPixel\DesignElements\Model\Config\Source;

class Accordions implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'simple', 'label' => __('Minimalist')],
            ['value' => 'accordion-bg', 'label' => __('With background')],
            ['value' => 'accordion-border', 'label' => __('With border')]
        ];
    }
}
