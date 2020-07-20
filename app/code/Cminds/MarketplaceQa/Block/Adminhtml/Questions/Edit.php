<?php

namespace Cminds\MarketplaceQa\Block\Adminhtml\Questions;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_questions';
        $this->_blockGroup = 'Cminds_MarketplaceQa';
        $this->_headerText = __('Questions');

        parent::_construct();
        $this->removeButton('add');

        $this->buttonList->update(
            'back',
            'onclick',
            "setLocation('" . $this->getUrl('marketplaceqa/questions/index') . "')"
        );
    }
}
