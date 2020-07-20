<?php

namespace Cminds\MarketplaceRma\Setup;

use Cminds\MarketplaceRma\Model\StatusFactory;
use Cminds\MarketplaceRma\Model\ReasonFactory;
use Cminds\MarketplaceRma\Model\TypeFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class InstallData
 *
 * @package Cminds\MarketplaceRma\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var StatusFactory
     */
    private $statusFactory;

    /**
     * @var ReasonFactory
     */
    private $reasonFactory;

    /**
     * @var TypeFactory
     */
    private $typeFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * InstallData constructor.
     *
     * @param StatusFactory $statusFactory
     * @param DateTime      $dateTime
     * @param ReasonFactory $reasonFactory
     * @param TypeFactory   $typeFactory
     */
    public function __construct(
        StatusFactory $statusFactory,
        DateTime $dateTime,
        ReasonFactory $reasonFactory,
        TypeFactory $typeFactory
    ) {
        $this->statusFactory = $statusFactory;
        $this->reasonFactory = $reasonFactory;
        $this->typeFactory = $typeFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * Install data.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $rmaDefaultStatuses = [
            [
                'name' => 'Returns Open',
                'created_at' => $this->dateTime->timestamp()
            ],
            [
                'name' => 'Returns Closed',
                'created_at' => $this->dateTime->timestamp()
            ],
            [
                'name' => 'Returns Approved',
                'created_at' => $this->dateTime->timestamp()
            ],
            [
                'name' => 'Returns Processing',
                'created_at' => $this->dateTime->timestamp()
            ]
        ];

        $rmaDefaultReasons = [
            [
                'name' => 'Item damaged',
                'created_at' => $this->dateTime->timestamp()
            ],
            [
                'name' => 'Package not complete',
                'created_at' => $this->dateTime->timestamp()
            ],
            [
                'name' => 'Wrong items',
                'created_at' => $this->dateTime->timestamp()
            ]
        ];

        $rmaDefaultTypes = [
            [
                'name' => 'Refund',
                'created_at' => $this->dateTime->timestamp()
            ],
            [
                'name' => 'Change size',
                'created_at' => $this->dateTime->timestamp()
            ],
            [
                'name' => 'Return',
                'created_at' => $this->dateTime->timestamp()
            ]
        ];

        /**
         * Insert default data to the status table.
         */
        foreach ($rmaDefaultStatuses as $data) {
            $this->createStatus()->setData($data)->save();
        }

        /**
         * Insert default data to the reason table.
         */
        foreach ($rmaDefaultReasons as $data) {
            $this->createReason()->setData($data)->save();
        }

        /**
         * Insert default data to the type table.
         */
        foreach ($rmaDefaultTypes as $data) {
            $this->createType()->setData($data)->save();
        }
    }

    /**
     * Create new Status instance.
     *
     * @return \Cminds\MarketplaceRma\Model\Status
     */
    public function createStatus()
    {
        return $this->statusFactory->create();
    }

    /**
     * Create new Reason instance
     *
     * @return mixed
     */
    public function createReason()
    {
        return $this->reasonFactory->create();
    }

    /**
     * Create new Type instance
     *
     * @return mixed
     */
    public function createType()
    {
        return $this->typeFactory->create();
    }
}
