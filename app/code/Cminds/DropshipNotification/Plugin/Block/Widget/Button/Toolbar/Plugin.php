<?php

namespace Cminds\DropshipNotification\Plugin\Block\Widget\Button\Toolbar;

use Cminds\DropshipNotification\Model\Config as ModuleConfig;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Sales\Block\Adminhtml\Order\View;
use Cminds\DropshipNotification\Helper\Data as Helper;

/**
 * Cminds DropshipNotification order view buttons plugin.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /** @var Helper */
    private $helper;


    /**
     * Plugin constructor.
     *
     * @param ModuleConfig      $moduleConfig
     * @param Helper            $helper
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        Helper $helper
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->helper = $helper;
    }

    /**
     * @param Toolbar       $toolbar
     * @param AbstractBlock $context
     * @param ButtonList    $buttonList
     *
     * @return array
     */
    public function beforePushButtons(
        Toolbar $toolbar,
        AbstractBlock $context,
        ButtonList $buttonList
    ) {
        if (!$context instanceof View) {
            return [$context, $buttonList];
        }

        if ($this->moduleConfig->isEnabled() === false) {
            return [$context, $buttonList];
        }

        $orderId = $context->getRequest()->getParam('order_id');
        if ($orderId && !$this->helper->isDropshipButtonAvailable($orderId)) {
            return [$context, $buttonList];
        }

        $buttonList->add(
            'order_dropship',
            [
                'label' => __('Dropship'),
                'onclick' => 'setLocation(\'' . $context->getUrl('dropshipnotification/order/sendNotifications') . '\')',
                'class' => 'dropship',
            ]
        );

        return [$context, $buttonList];
    }
}
