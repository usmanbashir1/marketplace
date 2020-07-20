<?php
namespace WeltPixel\TitleRewrite\Plugin\Category;

class DataProvider extends \WeltPixel\Backend\Plugin\Category\DataProvider
{

    /**
     * Rewrite this in all subclassess, provide the list with category attributes
     * @return array
     */
    protected function _getFieldsMap() {
        return [
            'general' => [
                'title_rewrite'
            ]
        ];
    }


}
