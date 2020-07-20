<?php
/**
 * Cminds MinimumOrderAmount module registration.
 *
 * @category Cminds
 * @package  Cminds_MarketplaceMinAmount
 * @author   Mateusz Niziolek <mateusz.niziolek@gmail.com>
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Cminds_MarketplaceMinAmount',
    __DIR__
);