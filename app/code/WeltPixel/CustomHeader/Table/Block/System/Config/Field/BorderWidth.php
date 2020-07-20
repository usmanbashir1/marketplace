<?php
namespace WeltPixel\CustomHeader\Table\Block\System\Config\Field;

class BorderWidth extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
	/**
	 * Grid columns
	 *
	 * @var array
	 */
	protected $_columns = [];
	/**
	 * Enable the "Add after" button or not
	 *
	 * @var bool
	 */
	protected $_addAfter = false;
	/**
	 * Label of add button
	 *
	 * @var string
	 */
	protected $_addButtonLabel = false;

	/**
	 * @var string
	 */
	protected $_template = 'WeltPixel_CustomHeader::system/config/form/field/array.phtml';

	/**
	 * Prepare to render
	 *
	 * @return void
	 */
	protected function _prepareToRender() {
		$this->addColumn(
			'border_width_top', [
				'label' => __('Top'),
			]
		);
		$this->addColumn(
			'border_width_right', [
				'label' => __('Right'),
			]
		);
		$this->addColumn(
			'border_width_bottom', [
				'label' => __('Bottom'),
			]
		);
		$this->addColumn(
			'border_width_left', [
				'label' => __('Left'),
			]
		);
		$this->_addAfter = false;
		$this->_addButtonLabel = false;
	}

	/**
	 * Render array cell for prototypeJS template
	 *
	 * @param string $columnName
	 * @return string
	 * @throws \Exception
	 */
	public function renderCellTemplate($columnName)
	{
		$arrValues = $this->getArrayRows();
		$values = count($arrValues) ? array_values($arrValues)[0]->getData() : [];

		if (empty($this->_columns[$columnName])) {
			throw new \Exception('Wrong column name specified.');
		}
		$column = $this->_columns[$columnName];
		$inputName = $this->_getCellInputElementName($columnName);

		if ($column['renderer']) {
			return $column['renderer']->setInputName(
				$inputName
			)->setInputId(
				$this->_getCellInputElementId('border-width', $columnName)
			)->setColumnName(
				$columnName
			)->setColumn(
				$column
			)->toHtml();
		}

		$valued = array_key_exists($columnName, $values) ? $values[$columnName] : '';

		return '<input type="text" id="' . $this->_getCellInputElementId(
			'border-width',
			$columnName
		) .
		'" name="' .
		$inputName .
		'" value="' . $valued . '"/>';
	}

	/**
	 * @return array
	 */
	public function getArrayRows()
	{
		$result = [];
		/** @var \Magento\Framework\Data\Form\Element\AbstractElement */
		$element = $this->getElement();

		/**
		 * Added this to take default values from config.xml
		 */
        try {
            $elementValue = $element->getValue();
            $elementValueJson = json_decode($element->getValue());
            /** magento 2.2 removed serialization  */
            if ($elementValueJson && ( $elementValue != $elementValueJson )) {
                $element->setValue(json_decode($element->getValue(), true));
            } else {
                $element->setValue(unserialize($element->getValue()));
            }
        } catch (\Exception $ex) {}

		if ($element->getValue() && is_array($element->getValue())) {
			foreach ($element->getValue() as $rowId => $row) {
				$rowColumnValues = [];
				foreach ($row as $key => $value) {
					$row[$key] = $value;
					$rowColumnValues[$this->_getCellInputElementId($rowId, $key)] = $row[$key];
				}
				$row['_id'] = $rowId;
				$row['column_values'] = $rowColumnValues;
				$result[$rowId] = new \Magento\Framework\DataObject($row);
				$this->_prepareArrayRow($result[$rowId]);
			}
		}
		return $result;
	}

}