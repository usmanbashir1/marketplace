<?php
namespace WeltPixel\DesignElements\Model\Config\Source;

class Toggles implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'simple', 'label' => __('Toggle')],
            ['value' => 'toggle-bg', 'label' => __('Toggle with background')],
            ['value' => 'toggle-border', 'label' => __('Toggle with border')]
        ];
    }
}
