<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class SortBy implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'none', 'label' => __('As they come from Instagram')],
            ['value' => 'most-recent', 'label' => __('Newest to oldest')],
            ['value' => 'least-recent', 'label' => __('Oldest to newest')],
            ['value' => 'most-liked', 'label' => __('Highest # of likes to lowest')],
            ['value' => 'least-liked', 'label' => __('Lowest # likes to highest')],
            ['value' => 'most-commented', 'label' => __('Highest # of comments to lowest')],
            ['value' => 'least-commented', 'label' => __('Lowest # of comments to highes')],
            ['value' => 'random', 'label' => __('Random order')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'none' => __('As they come from Instagram'),
            'most-recent' => __('Newest to oldest'),
            'least-recent' => __('Oldest to newest'),
            'most-liked' => __('Highest # of likes to lowest'),
            'least-liked' => __('Lowest # likes to highest'),
            'most-commented' => __('Highest # of comments to lowest'),
            'least-commented' => __('Lowest # of comments to highes'),
            'random' => __('Random')
        ];
    }
}
