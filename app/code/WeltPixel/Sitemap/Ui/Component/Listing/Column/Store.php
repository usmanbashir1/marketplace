<?php
namespace WeltPixel\Sitemap\Ui\Component\Listing\Column;

/**
 * Class Store
 */
class Store extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        if ($item[$this->storeKey] == 0) {
            return __('All Store Views');
        }

        return parent::prepareItem($item);
    }
}
