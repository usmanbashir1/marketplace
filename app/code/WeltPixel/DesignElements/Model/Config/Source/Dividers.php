<?php
namespace WeltPixel\DesignElements\Model\Config\Source;

class Dividers implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'standard', 'label' => __('Standard')],
            ['value' => 'divider-right', 'label' => __('Right Aligned')],
            ['value' => 'divider-center', 'label' => __('Center Aligned')],
            ['value' => 'divider-short', 'label' => __('Short Length')],
            ['value' => 'divider-short divider-right', 'label' => __('Short Right Aligned')],
            ['value' => 'divider-short divider-center', 'label' => __('Short Center Aligned')],
            ['value' => 'divider-rounded', 'label' => __('Rounded')],
            ['value' => 'divider-rounded divider-right', 'label' => __('Rounded Right Aligned')],
            ['value' => 'divider-rounded divider-center', 'label' => __('Rounded Center Aligned')],
            ['value' => 'divider-short divider-rounded', 'label' => __('Rounded Short Length')],
            ['value' => 'divider-short divider-rounded divider-right', 'label' => __('Rounded Short Right Aligned')],
            ['value' => 'divider-short divider-rounded divider-center', 'label' => __('Rounded Short Center Aligned')],
            ['value' => 'divider-border', 'label' => __('Bordered')],
            ['value' => 'divider-border divider-right', 'label' => __('Bordered Right Aligned')],
            ['value' => 'divider-border divider-center', 'label' => __('Bordered Center Aligned')],
            ['value' => 'divider-short divider-border', 'label' => __('Bordered Short Length')],
            ['value' => 'divider-short divider-border divider-right', 'label' => __('Bordered Short Right Aligned')],
            ['value' => 'divider-short divider-border divider-center', 'label' => __('Bordered Short Center Aligned')],
        ];
    }
}
