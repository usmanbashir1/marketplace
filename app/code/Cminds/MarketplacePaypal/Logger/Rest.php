<?php

namespace Cminds\MarketplacePaypal\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Rest Logger
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Rest extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/cminds_paypal/request.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}