<?php
namespace WeltPixel\NavigationLinks\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Product status functionality model
 */
class Displaymode implements SourceInterface, OptionSourceInterface
{
	
	/**
	 * menu display mode
	 */
	const DIPLAY_DEFAULT       = 'default';
	const DIPLAY_FULLWIDTH     = 'fullwidth';
	const DIPLAY_SECTIONED     = 'sectioned';
	const DIPLAY_BOXED         = 'boxed';
	
	
	/**
	 * Prepare display options.
	 *
	 * @return array
	 */
	public function getAvailableModes()
	{
		return [
				self::DIPLAY_DEFAULT       => __('Default'),
				self::DIPLAY_FULLWIDTH     => __('Full Width'),
				self::DIPLAY_SECTIONED     => __('Sectioned'),
				self::DIPLAY_BOXED         => __('Boxed'),
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
