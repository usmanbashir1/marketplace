<?php
namespace WeltPixel\DesignElements\Plugin\Category;

class DataProvider extends \WeltPixel\Backend\Plugin\Category\DataProvider
{

    /**
     * Rewrite this in all subclassess, provide the list with category attributes
     * @return array
     */
    protected function _getFieldsMap() {
        return [
            'weltpixel_options' => [
                'css_global',
                'css_phone_small',
                'css_phone',
                'css_tablet_small',
                'css_tablet',
                'css_desktop',
                'css_desktop_large',
                'custom_js'
            ]
        ];
    }


}
