<?php

namespace Cminds\MarketplaceRma\Block\Customer\Account\Navigation;

use Cminds\MarketplaceRma\Helper\Data;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Portal
 *
 * @package Cminds\MarketplaceRma\Block\Customer\Account\Navigation
 */
class Portal extends Current
{
    /**
     * @var DefaultPathInterface
     */
    protected $defaultPath;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Portal constructor.
     *
     * @param Context              $context
     * @param DefaultPathInterface $defaultPath
     * @param Data                 $helper
     * @param ModuleConfig         $moduleConfig
     * @param array                $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        Data $helper,
        ModuleConfig $moduleConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $defaultPath
        );

        $this->defaultPath = $defaultPath;
        $this->helper = $helper;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * If the module is not enabled in configuration then we need to prevent rendering.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->moduleConfig->isActive() === false) {
            return '';
        }

        if ($this->helper->isSupplier() === true) {
            return '';
        }

        return parent::_toHtml();
    }
}
