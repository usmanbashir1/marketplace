<?php

namespace Cminds\MarketplacePaypal\Block\Adminhtml\Billing;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Billing Pay
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Pay extends Container
{
    /**
     * Initialize form.
     * Add standard buttons.
     * Add "Save and Continue" button.
     *
     * @return void
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_billing';
        $this->_blockGroup = 'Cminds_MarketplacePaypal';
        $this->_mode = 'pay';

        parent::_construct();

        $this->buttonList->update(
            'back',
            'onclick',
            "setLocation('" . $this->getUrl('marketplace/billing/index') . "')"
        );
        $this->buttonList->update('save', 'label', __('Pay Using Paypal'));
    }
}
