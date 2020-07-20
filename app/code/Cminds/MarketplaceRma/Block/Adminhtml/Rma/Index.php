<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Index
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma
 */
class Index extends Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_rma_index';
        $this->_blockGroup = 'Cminds_MarketplaceRma';
        $this->_addButtonLabel = __('Add New');
        $this->_headerText = __('Marketplace Manage Returns');

        parent::_construct();
    }

    /**
     * Return create new supplier url.
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl(
            'marketplacerma/rma/create', ['is_new' => true]
        );
    }
}
