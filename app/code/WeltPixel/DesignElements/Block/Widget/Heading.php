<?php
namespace WeltPixel\DesignElements\Block\Widget;

class Heading extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/heading_widget.phtml');
    }
}