<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Status;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Edit
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Status
 */
class Edit extends Container
{
    public function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_status_edit';
        $this->_blockGroup = 'Cminds_MarketplaceRma';
        $this->_headerText = __('Marketplace Edit Status');

        $this->removeButton('add');

        parent::_construct();

        $this->buttonList->update(
            'back', 
            'onclick', 
            "setLocation('" . $this->getUrl('marketplacerma/status/index') . "')"
        );
    }
}
