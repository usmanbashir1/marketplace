<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Frontend\Model\Elastic\Adapter;

use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;

class FieldMapper
{
    /**
     * @param FieldMapperInterface $subject
     * @param $result
     * @param array $context
     * @return mixed
     */
    public function afterGetAllAttributesTypes(FieldMapperInterface $subject, $result, $context = [])
    {
        $result['created_at'] = [
            'type' => 'date',
            'fields' => [
                'sort_created_at' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];
        $result['wp_sortby_new'] = [
            'type' => 'date',
            'fields' => [
                'sort_wp_sortby_new' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];

        $result['wp_sortby_rates'] = [
            'type' => 'float',
            'fields' => [
                'sort_wp_sortby_rates' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];

        $result['wp_sortby_review'] = [
            'type' => 'float',
            'fields' => [
                'sort_wp_sortby_review' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];

        $result['wp_sortby_sales'] = [
            'type' => 'float',
            'fields' => [
                'sort_wp_sortby_sales' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];

        return $result;
    }
}
