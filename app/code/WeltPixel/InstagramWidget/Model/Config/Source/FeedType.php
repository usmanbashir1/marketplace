<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class FeedType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'user', 'label' => __('User')]
        ];
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return [
            'user' => __('User')
        ];
    }
}
