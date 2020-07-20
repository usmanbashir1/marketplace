<?php

namespace WeltPixel\GoogleCards\Model\Config\Source;

use Magento\Framework\DB\Ddl\Table;

/**
 * Class ItemConditionsOptions
 * @package WeltPixel\GoogleCards\Model\Config\Source
 */
class ItemConditionsOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Select Option'), 'value' => ''],
            ['label' => __('Damaged Condition'), 'value' => 'DamagedCondition'],
            ['label' => __('New Condition'), 'value' => 'NewCondition'],
            ['label' => __('Refurbished Condition'), 'value' => 'RefurbishedCondition'],
            ['label' => __('Used Condition'), 'value' => 'UsedCondition']
        ];
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
                'unsigned' => false,
                'default'  => null,
                'extra'    => null,
                'type'     => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment'  => 'Item Conditions Options  ' . $attributeCode . ' column',
            ],
        ];
    }
}