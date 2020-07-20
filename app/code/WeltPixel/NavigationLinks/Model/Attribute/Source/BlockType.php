<?php
namespace WeltPixel\NavigationLinks\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Product status functionality model
 */
class BlockType implements SourceInterface, OptionSourceInterface
{

    /**
     * block type
     */
    const BLOCK_NONE  = 'none';
    const BLOCK_CMS  = 'block_cms';
    const BLOCK_HTML = 'block';


    /**
     * Prepare display options.
     *
     * @return array
     */
    public function getAvailableModes()
    {
        return [
            self::BLOCK_NONE => __('None'),
            self::BLOCK_CMS  => __('CMS Block'),
            self::BLOCK_HTML => __('Custom HTML'),
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions() {
        $result = [];

        foreach ($this->getAvailableModes() as $index => $value) {
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
        $options = $this->getAvailableModes();

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
