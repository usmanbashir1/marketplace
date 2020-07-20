<?php

namespace Cminds\MarketplacePaypal\Model\Source;

use Cminds\MarketplacePaypal\Model\PaymentStatus;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Payout Status
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class PayoutStatus implements OptionSourceInterface
{
    const INCOMPLETE_STATUSES = [PaymentStatus::DENIED, PaymentStatus::CANCELED];
    const SUCCESS_STATUSES = [PaymentStatus::SUCCESS];
    const PENDING_STATUSES = [PaymentStatus::PROCESSING, PaymentStatus::PENDING];

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('Pending'),
                'value' => PaymentStatus::PENDING
            ],
            [
                'label' => __('Processing'),
                'value' => PaymentStatus::PROCESSING
            ],
            [
                'label' => __('Success'),
                'value' => PaymentStatus::SUCCESS
            ],
            [
                'label' => __('Canceled'),
                'value' => PaymentStatus::CANCELED
            ],
            [
                'label' => __('Denied'),
                'value' => PaymentStatus::DENIED
            ]
        ];
    }
}
