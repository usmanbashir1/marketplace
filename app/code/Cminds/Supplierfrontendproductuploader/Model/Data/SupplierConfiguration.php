<?php
namespace Cminds\Supplierfrontendproductuploader\Model\Data;

use Cminds\Supplierfrontendproductuploader\Api\Data\SupplierConfigurationInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Cminds Supplierfrontendproductuploader SupplierConfiguration.
 * @api
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 */

class SupplierConfiguration extends AbstractSimpleObject implements SupplierConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getSku(){
        return $this->_get(self::PARENT_SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku){
        $this->setData(self::PARENT_SKU, $sku);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(){
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name){
        $this->setData(self::NAME, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQty(){
        return $this->_get(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty){
        $this->setData(self::QTY, $qty);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight(){
        return $this->_get(self::WEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight){
        $this->setData(self::WEIGHT, $weight);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(){
        return $this->_get(self::ATTRIBUTES);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes = null ){
        $this->setData(self::ATTRIBUTES, $attributes);
        return $this;
    }

    /**
     * Data to array
     * @return array
     */
    public function toArray(){
        return $this->__toArray();
    }
}
