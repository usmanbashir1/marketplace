<?php
namespace WeltPixel\DesignElements\Block\Widget;

class Brand extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/brand_widget.phtml');
    }

    public function getImages()
    {
        $blockId = $this->getData('block_id');
        $result = array();
        $html = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($blockId)->toHtml();
        $html = strip_tags($html, '<a><img>');

        preg_match_all('~<a\s+.*?</a>|<img[^>]+>~is',$html, $result);

        $result = array_filter($result);

        if (isset($result[0]) && !empty($result)) {
            return $result[0];
        }
        return $result;
    }
}