<?php

namespace Cminds\MarketplaceQa\Block\Adminhtml\Questions;

use Magento\Backend\Block\Widget\Grid\Container;

class Index extends Container
{
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_controller = 'adminhtml_questions_index';
        $this->_blockGroup = 'Cminds_MarketplaceQa';
        $this->_headerText = __('Questions');

        parent::_construct();

        $this->removeButton('add');
    }
}
