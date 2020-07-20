<?php
namespace WeltPixel\Quickview\Block;

/**
 * Class Canonical
 * @package WeltPixel\Quickview\Block
 */
class Canonical extends \Magento\Catalog\Block\Product\View
{
    /**
     * Add meta canonical link
     * @return \Magento\Catalog\Block\Product\View
     */
    protected function _prepareLayout()
    {
        $product = $this->getProduct();
        if (!$product) {
            return parent::_prepareLayout();
        }

        $this->pageConfig->addRemotePageAsset(
            $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        return parent::_prepareLayout();
    }
}
