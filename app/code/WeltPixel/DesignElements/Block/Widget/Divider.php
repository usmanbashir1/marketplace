<?php
namespace WeltPixel\DesignElements\Block\Widget;

class Divider extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/divider_widget.phtml');
    }
}