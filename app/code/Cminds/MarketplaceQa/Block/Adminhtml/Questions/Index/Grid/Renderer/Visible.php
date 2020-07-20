<?php

namespace Cminds\MarketplaceQa\Block\Adminhtml\Questions\Index\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Backend\Block\Context;
use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject ;

class Visible extends AbstractRenderer
{
    protected $customer;

    public function __construct(
        Context $context,
        Customer $customer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->customer = $customer;
    }

    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        
        if ($value == 1) {
            return 'Yes';
        } else {
            return 'No';
        }

        
    }
}
