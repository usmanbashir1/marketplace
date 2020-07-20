<?php

namespace Cminds\MarketplacePaypal\Block\Plugin\Adminhtml\Billing\Billinglist\Grid\Renderer\Action;

use Cminds\Marketplace\Block\Adminhtml\Billing\Billinglist\Grid\Renderer\Action;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Cminds\MarketplacePaypal\Model\Config as ModuleConfig;
use Closure;

/**
 * Plugin
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Plugin
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Plugin constructor.
     * @param UrlInterface $urlBuilder
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ModuleConfig $moduleConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Around Render
     *
     * @param Action $subject
     * @param Closure $proceed
     * @param DataObject $row
     * @return mixed|string
     */
    public function aroundRender(
        Action $subject,
        Closure $proceed,
        DataObject $row
    ) {
        $html = $proceed($row);

        if ($this->moduleConfig->isActive() === false) {
            return $html;
        }

        $orderId = $row->getData('order_id');
        $supplierId = $row->getData('supplier_id');

        $addUrl = $this->urlBuilder->getUrl(
            'marketplacepaypal/billing/pay',
            ['order_id' => $orderId, 'supplier_id' => $supplierId]
        );
        $html .= ' | ' . sprintf("<a href='%s'>%s</a>", $addUrl, __('Create Paypal Payment'));

        return $html;
    }
}
