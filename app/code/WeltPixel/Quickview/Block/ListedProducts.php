<?php
namespace WeltPixel\Quickview\Block;

/**
 * Class ListedProducts
 * @package WeltPixel\Quickview\Block
 */
class ListedProducts extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array|null
     */
    private $productIds = null;

    /**
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _getProductIds()
    {
        if ($this->productIds === null) {
            $this->productIds = [];
            $productIds = [];
            $block = $this->getLayout()->getBlock('category.products.list');
            if (!$block) {
                $block = $this->getLayout()->getBlock('search_result_list');
            }
            if ($block) {
                $productCollection = $block->getLoadedProductCollection();
                /** @var $product \Magento\Catalog\Model\Product */
                foreach ($productCollection as $product) {
                    $productIds[] = $product->getId();
                }
            }
            $this->productIds = $productIds;
        }
        return $this->productIds;
    }

    /**
     * @return array[]
     */
    public function getProductIds()
    {
        return $this->_getProductIds();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->getProductIds()) {
            return '';
        }
        return parent::_toHtml();
    }
}
