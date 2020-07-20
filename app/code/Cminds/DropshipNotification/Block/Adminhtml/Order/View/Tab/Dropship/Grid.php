<?php

namespace Cminds\DropshipNotification\Block\Adminhtml\Order\View\Tab\Dropship;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;

/**
 * Cminds DropshipNotification admin order view dropship grid block.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Grid extends WidgetGrid
{
    /**
     * Collection object.
     *
     * @var \Magento\Framework\Data\Collection
     */
    private $collection;

    /**
     * Identifier of last grid column.
     *
     * @var string
     */
    private $lastColumnId;

    /**
     * Columns view order.
     *
     * @var array
     */
    private $columnsOrder = [];

    /**
     * Label for empty cell.
     *
     * @var string
     */
    private $emptyCellLabel = '';

    /**
     * Columns to group by.
     *
     * @var string[]
     */
    private $groupedColumn = [];

    /**
     * Empty grid text.
     *
     * @var string|null
     */
    protected $emptyText;

    /**
     * Empty grid text CSS class
     *
     * @var string|null
     */
    protected $emptyTextCss = 'empty-text';

    /**
     * @var string
     */
    protected $_template = 'Cminds_DropshipNotification::order/view/tab/dropship/grid.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->emptyText = __('We couldn\'t find any records.');
    }

    /**
     * Retrieve column set block.
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getColumnSet()
    {
        if (!$this->getChildBlock('grid.columnSet')) {
            $this->setChild(
                'grid.columnSet',
                $this
                    ->getLayout()
                    ->createBlock(\Magento\Backend\Block\Widget\Grid\ColumnSet::class)
            );
        }
        return parent::getColumnSet();
    }

    /**
     * Add column to grid
     *
     * @param string $columnId
     * @param array|\Magento\Framework\DataObject $column
     * @return $this
     * @throws \Exception
     */
    public function addColumn($columnId, $column)
    {
        if (is_array($column)) {
            $this->getColumnSet()->setChild(
                $columnId,
                $this->getLayout()
                    ->createBlock(\Magento\Backend\Block\Widget\Grid\Column\Extended::class)
                    ->setData($column)
                    ->setId($columnId)
                    ->setGrid($this)
            );
            $this->getColumnSet()->getChildBlock($columnId)->setGrid($this);
        } else {
            throw new \Exception(__('Please correct the column format and try again.'));
        }

        $this->lastColumnId = $columnId;

        return $this;
    }

    /**
     * Remove existing column
     *
     * @param string $columnId
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeColumn($columnId)
    {
        if ($this->getColumnSet()->getChildBlock($columnId)) {
            $this->getColumnSet()->unsetChild($columnId);
            if ($this->lastColumnId == $columnId) {
                $this->lastColumnId = array_pop($this->getColumnSet()->getChildNames());
            }
        }
        return $this;
    }

    /**
     * Add column to grid after specified column.
     *
     * @param   string                              $columnId
     * @param   array|\Magento\Framework\DataObject $column
     * @param   string                              $after
     *
     * @return  $this
     * @throws \Exception
     */
    public function addColumnAfter($columnId, $column, $after)
    {
        $this->addColumn($columnId, $column);
        $this->addColumnsOrder($columnId, $after);

        return $this;
    }

    /**
     * Add column view order
     *
     * @param string $columnId
     * @param string $after
     * @return $this
     */
    public function addColumnsOrder($columnId, $after)
    {
        $this->columnsOrder[$columnId] = $after;
        return $this;
    }

    /**
     * Retrieve columns order
     *
     * @return array
     */
    public function getColumnsOrder()
    {
        return $this->columnsOrder;
    }

    /**
     * Sort columns by predefined order
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sortColumnsByOrder()
    {
        foreach ($this->getColumnsOrder() as $columnId => $after) {
            $this->getLayout()->reorderChild(
                $this->getColumnSet()->getNameInLayout(),
                $this->getColumn($columnId)->getNameInLayout(),
                $this->getColumn($after)->getNameInLayout()
            );
        }

        $columns = $this->getColumnSet()->getChildNames();
        $this->lastColumnId = array_pop($columns);
        return $this;
    }

    /**
     * Retrieve identifier of last column
     *
     * @return string
     */
    public function getLastColumnId()
    {
        return $this->lastColumnId;
    }

    /**
     * Initialize grid columns
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareColumns()
    {
        $this->sortColumnsByOrder();

        return $this;
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            if ($this->getCollection()->isLoaded()) {
                $this->getCollection()->clear();
            }

            $this->getCollection()->load();
            $this->_afterLoadCollection();
        }

        return $this;
    }

    /**
     * Process collection after loading
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        return $this;
    }

    /**
     * Initialize grid before rendering
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareGrid()
    {
        $this->_prepareColumns();
        parent::_prepareGrid();

        return $this;
    }

    /**
     * Check whether should render cell
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return boolean
     */
    public function shouldRenderCell($item, $column)
    {
        if ($this->isColumnGrouped($column) && $item->getIsEmpty()) {
            return true;
        }
        if (!$item->getIsEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve label for empty cell
     *
     * @return string
     */
    public function getEmptyCellLabel()
    {
        return $this->emptyCellLabel;
    }

    /**
     * Set label for empty cell
     *
     * @param string $label
     * @return $this
     */
    public function setEmptyCellLabel($label)
    {
        $this->emptyCellLabel = $label;

        return $this;
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * Get children of specified item
     *
     * @param \Magento\Framework\DataObject $item
     * @return array
     */
    public function getMultipleRows($item)
    {
        return $item->getChildren();
    }

    /**
     * Retrieve columns for multiple rows
     * @return array
     */
    public function getMultipleRowColumns()
    {
        $columns = $this->getColumns();
        foreach ($this->groupedColumn as $column) {
            unset($columns[$column]);
        }
        return $columns;
    }

    /**
     * Retrieve rowspan number
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return int|false
     */
    public function getRowspan($item, $column)
    {
        if ($this->isColumnGrouped($column)) {
            return count($this->getMultipleRows($item)) + count($this->groupedColumn);
        }
        return false;
    }

    /**
     * Check whether given column is grouped
     *
     * @param string|object $column
     * @param string $value
     * @return boolean|$this
     */
    public function isColumnGrouped($column, $value = null)
    {
        if (null === $value) {
            if (is_object($column)) {
                return in_array($column->getIndex(), $this->groupedColumn);
            }
            return in_array($column, $this->groupedColumn);
        }
        $this->groupedColumn[] = $column;
        return $this;
    }

    /**
     * Check whether should render empty cell
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return boolean
     */
    public function shouldRenderEmptyCell($item, $column)
    {
        return $item->getIsEmpty() && in_array($column['index'], $this->groupedColumn);
    }

    /**
     * Retrieve colspan for empty cell
     *
     * @return int
     */
    public function getEmptyCellColspan()
    {
        return $this->getColumnCount() - count($this->groupedColumn);
    }

    /**
     * Count columns
     *
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->getColumns());
    }

    /**
     * Set empty text for grid
     *
     * @param string $text
     * @return $this
     */
    public function setEmptyText($text)
    {
        $this->emptyText = $text;
        return $this;
    }

    /**
     * Return empty text for grid
     *
     * @return string
     */
    public function getEmptyText()
    {
        return $this->emptyText;
    }

    /**
     * Set empty text CSS class
     *
     * @param string $cssClass
     * @return $this
     */
    public function setEmptyTextClass($cssClass)
    {
        $this->emptyTextCss = $cssClass;
        return $this;
    }

    /**
     * Return empty text CSS class
     *
     * @return string
     */
    public function getEmptyTextClass()
    {
        return $this->emptyTextCss;
    }

    /**
     * Set collection object
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return void
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * get collection object
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getGridTitle()
    {
        return '';
    }
}
