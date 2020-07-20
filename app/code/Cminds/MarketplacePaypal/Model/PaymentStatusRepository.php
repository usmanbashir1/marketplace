<?php

namespace Cminds\MarketplacePaypal\Model;

use Cminds\MarketplacePaypal\Model\PaymentStatus;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Cminds\MarketplacePaypal\Model\ResourceModel\PaymentStatus as PaymentStatusResource;
use Cminds\MarketplacePaypal\Model\ResourceModel\PaymentStatus\Collection;

/**
 * Payment Status Repository
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class PaymentStatusRepository
{
    /**
     * @var PaymentStatusResource
     */
    protected $resource;

    /**
     * @var PaymentStatusFactory
     */
    protected $paymentStatusFactory;

    /**
     * PaymentStatusRepository constructor.
     * @param PaymentStatusResource $resource
     * @param PaymentStatusFactory $paymentStatusFactory
     */
    public function __construct(
        PaymentStatusResource $resource,
        PaymentStatusFactory $paymentStatusFactory
    ) {
        $this->resource = $resource;
        $this->paymentStatusFactory = $paymentStatusFactory;
    }

    /**
     * Save
     *
     * @param \Cminds\MarketplacePaypal\Model\PaymentStatus $paymentStatus
     * @return \Cminds\MarketplacePaypal\Model\PaymentStatus
     * @throws CouldNotSaveException
     */
    public function save(PaymentStatus $paymentStatus): PaymentStatus
    {
        try {
            $this->resource->save($paymentStatus);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $paymentStatus;
    }

    /**
     * Get by Id
     *
     * @param int $payoutId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById(int $payoutId)
    {
        $paymentStatus = $this->paymentStatusFactory->create();
        $this->resource->load($paymentStatus, $payoutId);
        if (!$paymentStatus->getId()) {
            throw new NoSuchEntityException(__('The payout payment with the "%1" ID doesn\'t exist.', $restrictionId));
        }

        return $paymentStatus;
    }

    /**
     * Get list of tracking payout statuses
     *
     * @param array $statuses
     *
     * @return Collection
     */
    public function getList($statuses)
    {
        $paymentStatuses = $this->paymentStatusFactory->create();
        $collection = $paymentStatuses->getCollection()
            ->addFieldToFilter('status', ['in' => $statuses]);
        return $collection;
    }

    /**
     * Delete Restriction
     *
     * @param Restriction $restriction
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Restriction $restriction)
    {
        try {
            $this->resource->delete($restriction);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        
        return true;
    }

    /**
     * Delete Restriction by given Restriction Identity
     *
     * @param string $restrictionId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($restrictionId)
    {
        return $this->delete($this->getById($restrictionId));
    }
}
