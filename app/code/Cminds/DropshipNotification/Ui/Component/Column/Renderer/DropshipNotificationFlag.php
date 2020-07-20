<?php

namespace Cminds\DropshipNotification\Ui\Component\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Cminds DropshipNotification column renderer for dropship notification flag.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class DropshipNotificationFlag extends AbstractRenderer
{
    /**
     * @param DataObject $row
     *
     * @return string
     */
    protected function _getValue(DataObject $row)
    {
        $value = (bool)parent::_getValue($row);

        return $value ? __('Completed') : __('Incomplete');
    }
}
