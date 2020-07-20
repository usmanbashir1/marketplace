<?php
namespace WeltPixel\CategoryPage\Plugin\Category;

class DataProvider extends \WeltPixel\Backend\Plugin\Category\DataProvider
{

    /**
     * Rewrite this in all subclassess, provide the list with category attributes
     * @return array
     */
    protected function _getFieldsMap() {
        return [
            'weltpixel_options' => [
                'weltpixel_hide_title',
                'weltpixel_hide_breadcrumbs'
            ]
        ];
    }


}
