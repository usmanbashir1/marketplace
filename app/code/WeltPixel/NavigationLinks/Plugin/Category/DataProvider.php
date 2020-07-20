<?php
namespace WeltPixel\NavigationLinks\Plugin\Category;

class DataProvider extends \WeltPixel\Backend\Plugin\Category\DataProvider
{

    /**
     * Rewrite this in all subclassess, provide the list with category attributes
     * @return array
     */
    protected function _getFieldsMap() {
        return [
            'weltpixel_options' => [
                'weltpixel_category_url',
                'weltpixel_category_url_newtab'
            ],
            'weltpixel_megamenu' => [
                'weltpixel_mm_display_mode',
                'weltpixel_mm_columns_number',
                'weltpixel_mm_column_width',
                'weltpixel_mm_top_block_type',
                'weltpixel_mm_top_block_cms',
                'weltpixel_mm_top_block',
                'weltpixel_mm_right_block_type',
                'weltpixel_mm_right_block_cms',
                'weltpixel_mm_right_block',
                'weltpixel_mm_bottom_block_type',
                'weltpixel_mm_bottom_block_cms',
                'weltpixel_mm_bottom_block',
                'weltpixel_mm_left_block_type',
                'weltpixel_mm_left_block_cms',
                'weltpixel_mm_left_block',
                'weltpixel_mm_mob_hide_allcat'
            ]
        ];
    }


}
