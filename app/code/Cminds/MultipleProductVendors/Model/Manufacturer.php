<?php

namespace Cminds\MultipleProductVendors\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Cminds\MultipleProductVendors\Model\ResourceModel\Manufacturer\CollectionFactory as ManufacturerCollectionFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Manufacturer extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'product_manufacturer_codes';

    /**
     * Cache tag.
     *
     * @var string
     */
    protected $cacheTag = 'product_manufacturer_codes';

    /**
     * Event prefix.
     *
     * @var string
     */
    protected $eventPrefix = 'product_manufacturer_code';

    /**
     * Manufacturer code collection factory.
     *
     * @var ManufacturerCollectionFactory
     */
    private $manufacturerCollectionFactory;

    /**
     * Manufacturer constructor.
     *
     * @param ManufacturerCollectionFactory $manufacturerCollectionFactory
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        ManufacturerCollectionFactory $manufacturerCollectionFactory,
        Context $context,
        Registry $registry
    ) {
        parent::__construct(
            $context,
            $registry
        );

        $this->manufacturerCollectionFactory = $manufacturerCollectionFactory;
    }

    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cminds\MultipleProductVendors\Model\ResourceModel\Manufacturer');
    }

    /**
     * Save new code entity into the database.
     *
     * @param string $code
     *
     * @return Manufacturer|null
     */
    public function saveNewCode($code)
    {
        if (!is_string($code)) {
            return $this;
        }

        $codes = $this->manufacturerCollectionFactory->create()
            ->addFieldToFilter('manufacturer_code', $code)
            ->load();

        if ($codes->getSize()) {
            return $this;
        }

        $this
            ->setManufacturerCode($code)
            ->save();

        return $this;
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
