<?php
namespace WeltPixel\DesignElements\Block\Widget;

class Icon extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/icon_widget.phtml');
    }
}
