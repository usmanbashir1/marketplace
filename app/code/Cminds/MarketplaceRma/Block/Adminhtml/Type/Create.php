<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Type;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Create
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Type
 */
class Create extends Container
{
    public function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_type_create';
        $this->_blockGroup = 'Cminds_MarketplaceRma';
        $this->_headerText = __('Marketplace Create Type');

        $this->removeButton('add');

        parent::_construct();

        $this->buttonList->update(
            'back', 
            'onclick', 
            "setLocation('" . $this->getUrl('marketplacerma/type/index') . "')"
        );
    }
}
