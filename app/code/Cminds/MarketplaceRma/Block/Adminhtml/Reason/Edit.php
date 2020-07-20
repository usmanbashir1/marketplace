<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Reason;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Edit
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Reason
 */
class Edit extends Container
{
    public function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_reason_edit';
        $this->_blockGroup = 'Cminds_MarketplaceRma';
        $this->_headerText = __('Marketplace Edit Reason');

        $this->removeButton('add');

        parent::_construct();

        $this->buttonList->update(
            'back', 
            'onclick', 
            "setLocation('" . $this->getUrl('marketplacerma/reason/index') . "')"
        );
    }
}
