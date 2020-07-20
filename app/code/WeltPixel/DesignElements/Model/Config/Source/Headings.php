<?php
namespace WeltPixel\DesignElements\Model\Config\Source;

class Headings implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'h1 heading-block', 'label' => __('Heading Block H1')],
            ['value' => 'h2 heading-block', 'label' => __('Heading Block H2')],
            ['value' => 'h3 heading-block', 'label' => __('Heading Block H3')],
            ['value' => 'h4 heading-block', 'label' => __('Heading Block H4')],
            ['value' => 'h1 heading-block center', 'label' => __('Centered Heading Block H1')],
            ['value' => 'h2 heading-block center', 'label' => __('Centered Heading Block H2')],
            ['value' => 'h3 heading-block center', 'label' => __('Centered Heading Block H3')],
            ['value' => 'h4 heading-block center', 'label' => __('Centered Heading Block H4')],
            ['value' => 'h1 title-block', 'label' => __('Heading h1')],
            ['value' => 'h2 title-block', 'label' => __('Heading h2')],
            ['value' => 'h3 title-block', 'label' => __('Heading h3')],
            ['value' => 'h4 title-block', 'label' => __('Heading h4')],
            ['value' => 'h1 fancy-title title-double-border', 'label' => __('Double-border title h1')],
            ['value' => 'h2 fancy-title title-double-border', 'label' => __('Double-border title h2')],
            ['value' => 'h3 fancy-title title-double-border', 'label' => __('Double-border title h3')],
            ['value' => 'h4 fancy-title title-double-border', 'label' => __('Double-border title h4')],
            ['value' => 'h5 fancy-title title-double-border', 'label' => __('Double-border title h5')],
            ['value' => 'h6 fancy-title title-double-border', 'label' => __('Double-border title h6')],
            ['value' => 'h1 fancy-title title-border', 'label' => __('Single-border title h1')],
            ['value' => 'h2 fancy-title title-border', 'label' => __('Single-border title h2')],
            ['value' => 'h3 fancy-title title-border', 'label' => __('Single-border title h3')],
            ['value' => 'h4 fancy-title title-border', 'label' => __('Single-border title h4')],
            ['value' => 'h5 fancy-title title-border', 'label' => __('Single-border title h5')],
            ['value' => 'h6 fancy-title title-border', 'label' => __('Single-border title h6')],
            ['value' => 'h1 fancy-title title-border-color title-right', 'label' => __('Right-aligned Title with border-color h1')],
            ['value' => 'h2 fancy-title title-border-color title-right', 'label' => __('Right-aligned Title with border-color h2')],
            ['value' => 'h3 fancy-title title-border-color title-right', 'label' => __('Right-aligned Title with border-color h3')],
            ['value' => 'h4 fancy-title title-border-color title-right', 'label' => __('Right-aligned Title with border-color h4')],
            ['value' => 'h5 fancy-title title-border-color title-right', 'label' => __('Right-aligned Title with border-color h5')],
            ['value' => 'h6 fancy-title title-border-color title-right', 'label' => __('Right-aligned Title with border-color h6')],
            ['value' => 'h1 fancy-title title-bottom-border', 'label' => __('Title with colored bottom-border h1')],
            ['value' => 'h2 fancy-title title-bottom-border', 'label' => __('Title with colored bottom-border h2')],
            ['value' => 'h3 fancy-title title-bottom-border', 'label' => __('Title with colored bottom-border h3')],
            ['value' => 'h4 fancy-title title-bottom-border', 'label' => __('Title with colored bottom-border h4')],
            ['value' => 'h5 fancy-title title-bottom-border', 'label' => __('Title with colored bottom-border h5')],
            ['value' => 'h6 fancy-title title-bottom-border', 'label' => __('Title with colored bottom-border h6')],
            ['value' => 'h1 fancy-title title-dotted-border title-center', 'label' => __('Centered Title with dotted border h1')],
            ['value' => 'h2 fancy-title title-dotted-border title-center', 'label' => __('Centered Title with dotted border h2')],
            ['value' => 'h3 fancy-title title-dotted-border title-center', 'label' => __('Centered Title with dotted border h3')],
            ['value' => 'h4 fancy-title title-dotted-border title-center', 'label' => __('Centered Title with dotted border h4')],
            ['value' => 'h5 fancy-title title-dotted-border title-center', 'label' => __('Centered Title with dotted border h5')],
            ['value' => 'h6 fancy-title title-dotted-border title-center', 'label' => __('Centered Title with dotted border h6')],
            ['value' => 'emphasis-title', 'label' => __('Emphasis-title')],
            ['value' => 'text-rotater', 'label' => __('Text-rotater')],
            ['value' => 'dropcap', 'label' => __('Dropcap and Highlighted Text')],
        ];
    }
}
