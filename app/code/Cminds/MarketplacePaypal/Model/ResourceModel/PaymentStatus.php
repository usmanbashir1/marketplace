<?php
namespace Cminds\MarketplacePaypal\Model\ResourceModel;

use Cminds\MarketplacePaypal\Model\Source\PayoutStatus;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Cminds\Marketplace\Model\PaymentFactory;

/**
 * Statuses Collection
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class PaymentStatus extends AbstractDb
{
    /**
     * @var PaymentFactory
     */
    protected $paymentFactory;

    /**
     * PaymentStatus constructor.
     * @param Context $context
     * @param PaymentFactory $paymentFactory
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        PaymentFactory $paymentFactory,
        string $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->paymentFactory = $paymentFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cminds_marketplace_payout_status', 'entity_id');
    }

    /**
     * Before save
     *
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $payment = $this->paymentFactory->create();
            $payment
                ->setSupplierId($object->getSupplierId())
                ->setOrderId($object->getOrderId())
                ->setAmount($object->getAmount())
                ->setPaymentDate($object->getPaymentDate());
            $payment->save();
            $object->setPaymentId($payment->getId());
        }

        if (!$object->isObjectNew()) {
            $status = $object->getStatus();
            if (in_array($status, PayoutStatus::INCOMPLETE_STATUSES)) {
                $this->dropPayment($object->getPaymentId());
            }
        }

        return parent::_beforeSave($object);
    }

    /**
     * Drop payment by payment id
     *
     * @param int $paymentId
     */
    protected function dropPayment(int $paymentId)
    {
        $payment = $this->paymentFactory->create();
        $payment->getResource()->load($payment, $paymentId);
        $payment->getResource()->delete($payment);
    }
}
