<?php
namespace WeltPixel\ProductLabels\Model\ProductLabels\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Positions
 * @package WeltPixel\ProductLabels\Model\ProductLabels\Attribute\Source
 */
class Positions implements SourceInterface, OptionSourceInterface
{

    /**
     * Frequencies
     */
    const POSITION_TOP_LEFT         = 1;
    const POSITION_TOP_CENTER       = 2;
    const POSITION_TOP_RIGHT        = 3;
    const POSITION_MIDDLE_LEFT      = 4;
    const POSITION_MIDDLE_CENTER    = 5;
    const POSITION_MIDDLE_RIGHT     = 6;
    const POSITION_BOTTOM_LEFT      = 7;
    const POSITION_BOTTOM_CENTER    = 8;
    const POSITION_BOTTOM_RIGHT     = 9;
    const POSITION_OTHER            = 10;


    /**
     * @return array
     */
    public function getAvailablePositions()
    {
        return [
            self::POSITION_TOP_LEFT      => __('Top Left'),
            self::POSITION_TOP_CENTER    => __('Top Center'),
            self::POSITION_TOP_RIGHT     => __('Top Right'),
            self::POSITION_MIDDLE_LEFT   => __('Middle Left'),
            self::POSITION_MIDDLE_CENTER => __('Middle Center'),
            self::POSITION_MIDDLE_RIGHT  => __('Middle Right'),
            self::POSITION_BOTTOM_LEFT   => __('Bottom Left'),
            self::POSITION_BOTTOM_CENTER => __('Bottom Center'),
            self::POSITION_BOTTOM_RIGHT  => __('Bottom Right'),
            self::POSITION_OTHER         => __('Bellow Product Details'),
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions() {
        $result = [];

        foreach ($this->getAvailablePositions() as $index => $value) {
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
        $options = $this->getAvailablePositions();

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
