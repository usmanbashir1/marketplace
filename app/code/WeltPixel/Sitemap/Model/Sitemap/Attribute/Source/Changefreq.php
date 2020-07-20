<?php
namespace WeltPixel\Sitemap\Model\Sitemap\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Product status functionality model
 */
class Changefreq implements SourceInterface, OptionSourceInterface
{

    /**
     * Frequencies
     */
    const CHANGEFREQ_ALWAYS     = 'always';
    const CHANGEFREQ_HOURLY     = 'hourly';
    const CHANGEFREQ_DAILY      = 'daily';
    const CHANGEFREQ_WEEKLY     = 'weekly';
    const CHANGEFREQ_MONTHLY    = 'monthly';
    const CHANGEFREQ_NEVER      = 'never';


    /**
     * Prepare frequency options.
     *
     * @return array
     */
    public function getAvailableFrequencies()
    {
        return [
            self::CHANGEFREQ_ALWAYS     => __('Always'),
            self::CHANGEFREQ_HOURLY     => __('Hourly'),
            self::CHANGEFREQ_DAILY      => __('Daily'),
            self::CHANGEFREQ_WEEKLY     => __('Weekly'),
            self::CHANGEFREQ_MONTHLY    => __('Monthly'),
            self::CHANGEFREQ_NEVER      => __('Never')
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions() {
        $result = [];

        foreach ($this->getAvailableFrequencies() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value) {
        $options = $this->getAvailableFrequencies();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
