<?php

namespace Cminds\MarketplacePaypal\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Billing
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Billing extends Container
{
    /**
     * Object initialization.
     *
     * @return void
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_controller = 'billing';
        $this->_headerText = __('Billing');
        $this->_addButtonLabel = __('Add New Billing');

        parent::_construct();
    }
}
