<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Frontend\Model\ResourceModel\Fulltext;

use Magento\Framework\DB\Select;

class Collection
{

    /**
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject
     */
    /**
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject
     * @param string $attribute
     * @param string $dir
     * @return array
     */
    public function beforeSetOrder($subject, $attribute, $dir = Select::SQL_DESC)
    {
        if (strpos($attribute, '~') !== false) {
            $sortOptions = explode('~', $attribute);
            $attribute = $sortOptions[0];
            $dir = $sortOptions[1];
        }

        switch ($attribute) {
            case 'new_arrivals':
                $attribute = 'wp_sortby_new';
                break;
            case 'top_rated':
                $attribute = 'wp_sortby_rates';
                break;
            case 'most_reviewed':
                $attribute = 'wp_sortby_review';
                break;
            case 'top_seller':
                $attribute = 'wp_sortby_sales';
                break;
            default:
               break;
        }

        return [$attribute, $dir];
    }
}