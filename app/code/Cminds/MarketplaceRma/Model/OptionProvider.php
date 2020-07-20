<?php

namespace Cminds\MarketplaceRma\Model;

use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\Status\Collection as StatusCollection;
use Cminds\MarketplaceRma\Model\ResourceModel\Type\Collection as TypeCollection;
use Cminds\MarketplaceRma\Model\ResourceModel\Reason\Collection as ReasonCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Class OptionProvider
 *
 * @package Cminds\MarketplaceRma\Model
 */
class OptionProvider
{
    /**
     * @var StatusCollection
     */
    private $statusCollection;

    /**
     * @var TypeCollection
     */
    private $typeCollection;

    /**
     * @var ReasonCollection
     */
    private $reasonCollection;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * OptionProvider constructor.
     *
     * @param StatusCollection          $statusCollection
     * @param TypeCollection            $typeCollection
     * @param ReasonCollection          $reasonCollection
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param OrderCollectionFactory    $orderCollectionFactory
     * @param RmaCollectionFactory      $rmaCollectionFactory
     */
    public function __construct(
        StatusCollection $statusCollection,
        TypeCollection $typeCollection,
        ReasonCollection $reasonCollection,
        CustomerCollectionFactory $customerCollectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        RmaCollectionFactory $rmaCollectionFactory
    ) {
        $this->statusCollection = $statusCollection;
        $this->typeCollection = $typeCollection;
        $this->reasonCollection = $reasonCollection;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
    }

    /**
     * Get available statuses in option array for select.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $collection = $this->statusCollection->load()->getItems();
        $optionArray = [];

        foreach ($collection as $item) {
            $optionArray[] = [
                'value' => $item->getId(),
                'label' => $item->getData('name')
            ];
        }

        return $optionArray;
    }

    /**
     * Get available statuses in option array for index grid.
     *
     * @return array
     */
    public function getAvailableStatusesForIndexGrid()
    {
        $collection = $this->statusCollection->load()->getItems();
        $optionArray = [];

        foreach ($collection as $item) {
            $optionArray[$item->getId()] = $item->getData('name');
        }

        return $optionArray;
    }

    /**
     * Get available types in option array for select.
     *
     * @return array
     */
    public function getAvailableTypes()
    {
        $collection = $this->typeCollection->load()->getItems();
        $optionArray = [];

        foreach ($collection as $item) {
            $optionArray[] = [
                'value' => $item->getId(),
                'label' => $item->getData('name')
            ];
        }

        return $optionArray;
    }

    /**
     * Get available reasons in option array for select.
     *
     * @return array
     */
    public function getAvailableReasons()
    {
        $collection = $this->reasonCollection->load()->getItems();
        $optionArray = [];

        foreach ($collection as $item) {
            $optionArray[] = [
                'value' => $item->getId(),
                'label' => $item->getData('name')
            ];
        }

        return $optionArray;
    }

    /**
     * Get all customers.
     *
     * @return array
     */
    public function getCustomers()
    {
        $collection = $this->customerCollectionFactory->create()->getItems();
        $optionArray = [];

        $optionArray[0] = [
            'value' => 0,
            'label' => __('Please Select Customer')
        ];
        foreach ($collection as $item) {
            $optionArray[] = [
                'value' => $item->getId(),
                'label' => $item->getFirstname() . ' ' . $item->getLastname()
            ];
        }

        return $optionArray;
    }

    /**
     * Get customer orders, but only invoiced.
     *
     * @param $customerId
     *
     * @return array
     */
    public function getCustomerOrders($customerId)
    {
        $collection = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->getItems();
        $optionArray = [];

        $optionArray[0] = [
            'value' => 0,
            'label' => __('Please Select Order')
        ];
        foreach ($collection as $item) {
            if ($item->hasInvoices() && $this->checkIsRmaExistForOrder($item->getId()) === false) {
                $optionArray[] = [
                    'value' => $item->getId(),
                    'label' => $item->getIncrementId(),
                ];
            }
        }

        return $optionArray;
    }

    /**
     * Check is Returns request already exists for specific order.
     *
     * @param $orderId
     *
     * @return bool
     */
    public function checkIsRmaExistForOrder($orderId)
    {
        $collection = $this->rmaCollectionFactory
            ->create()
            ->addFieldToFilter('order_id', $orderId);

        if ($collection->count() > 0) {
            return true;
        }

        return false;
    }
}
