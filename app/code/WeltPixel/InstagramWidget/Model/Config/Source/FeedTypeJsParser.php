<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class FeedTypeJsParser implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'user', 'label' => __('User')],
            ['value' => 'tagged', 'label' => __('Tag')]
        ];
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return [
            'user' => __('User'),
            'tagged' => __('Tag')
        ];
    }
}
