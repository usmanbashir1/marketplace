<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;

/**
 * Class Edit
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma
 */
class Edit extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Edit constructor.
     *
     * @param Context          $context
     * @param EncoderInterface $jsonEncoder
     * @param Session          $authSession
     * @param array            $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('marketplacerma_rma_edit_tabs');
        $this->setDestElementId('marketplacerma-rma-edit-form');
        $this->setTitle(__('Returns'));
    }

    /**
     * Prepare layout.
     *
     * @return \Magento\Backend\Block\Widget\Tabs
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        // add buttons
        $this->getToolbar()->addChild(
            'backButton',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Back'),
                'onclick' => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
                'class' => 'back'
            ]
        );

        $this->getToolbar()->addChild(
            'resetButton',
            \Magento\Backend\Block\Widget\Button::class,
            ['label' => __('Reset'), 'onclick' => 'window.location.reload()', 'class' => 'reset']
        );

        if (intval($this->getRequest()->getParam('id'))) {
            $this->getToolbar()->addChild(
                'deleteButton',
                \Magento\Backend\Block\Widget\Button::class,
                [
                    'label' => __('Delete'),
                    'onclick' => 'deleteConfirm(\'' . __(
                            'Are you sure you want to do this?'
                        ) . '\', \'' . $this->getUrl(
                            '*/*/delete',
                            ['id' => $this->getRequest()->getParam('id')]
                        ) . '\')',
                    'class' => 'delete'
                ]
            );
        }

        $this->getToolbar()->addChild(
            'saveButton',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Save Returns'),
                'class' => 'save primary save-role',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'submit', 'target' => '#marketplacerma-rma-edit-form']],
                ]
            ]
        );

        // add tabs
        $this->addTab(
            'marketplacerma-rma-edit-form',
            $this->getLayout()->createBlock(\Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs\General::class)
        );

        $this->addTab(
            'marketplacerma-rma-products',
            $this->getLayout()->createBlock(\Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs\Product::class)
        );

        $this->addTab(
            'marketplacerma-rma-credit-memo',
            $this->getLayout()->createBlock(\Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs\Creditmemo::class)
        );

        $this->addTab(
            'marketplacerma-rma-customer-address',
            $this->getLayout()->createBlock(\Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs\CustomerAddress::class)
        );

        $this->addTab(
            'marketplacerma-rma-notes',
            $this->getLayout()->createBlock(\Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs\Notes::class)
        );

        return parent::_prepareLayout();
    }
}

