<?php

namespace Cminds\MarketplaceQa\Block\Adminhtml\Questions\Index\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Approved extends AbstractRenderer
{
    protected function _getValue(DataObject $row) // @codingStandardsIgnoreLine
    {
        $answer = $row->getData('answer');
        $status = (int)$row->getData('approved');

        if ($answer === null) {
            return '---';
        }

        if ($status === 1) {
            $label = __('Disapprove');
            $action = 'disapprove';
        } else {
            $label = __('Approve');
            $action = 'approve';
        }

        $url = $this->getUrl(
            'marketplaceqa/questions/' . $action,
            ['id' => $row->getId()]
        );

        return '<a href="' . $url . '">' . $label . '</a>';
    }
}
