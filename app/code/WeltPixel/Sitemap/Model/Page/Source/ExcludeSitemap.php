<?php
namespace WeltPixel\Sitemap\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ExcludeSitemap
 */
class ExcludeSitemap implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = [0 => __('No'), 1 => __('Yes')];
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
