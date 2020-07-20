<?php

namespace Cminds\DropshipNotification\Plugin;

use Cminds\DropshipNotification\Model\Config as ModuleConfig;
use Cminds\Marketplace\Block\Order\OrderList\Buttons as ButtonsBlock;
use Cminds\DropshipNotification\Helper\Data as Helper;
use \Magento\Framework\UrlInterface;

class AddDropShipButton
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /** @var Helper */
    private $helper;

    /**
     * @var UrlInterface $urlBuilder
     */
    private $urlBuilder;

    /**
     * Plugin constructor.
     *
     * @param ModuleConfig      $moduleConfig
     * @param Helper            $helper
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        Helper $helper,
        UrlInterface $urlBuilder
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param ButtonsBlock $subject
     * @param array|null $buttonList
     *
     * @return array
     */
    public function afterGetButtons(
        ButtonsBlock $subject,
        $buttonList
    ) {

        if ($this->moduleConfig->isEnabled() === false) {
            return $buttonList;
        }

        $order = $subject->getOrder();
        $orderId = $order->getId();

        if ($orderId && !$this->helper->isDropshipButtonAvailable($orderId)) {
            return $buttonList;
        }

        return $buttonList;
    }

    /**
     * Get dropship btn params
     *
     * @param int $orderId
     * @return array
     */
    protected function getDropshipButton($orderId)
    {
        return [
            'url' => $this->getOrderDropshipUrl($orderId),
            'class' => 'btn btn-primary',
            'title' => __('Drop Ship')
        ];
    }

    /**
     * Get dropship url
     *
     * @param int $orderId
     * @return string
     */
    protected function getOrderDropshipUrl($orderId)
    {
        return $this->urlBuilder->getUrl('dropshipnotification/dropship/index', ['order_id' => $orderId]);
    }
}
