<?php
namespace Cminds\Supplierfrontendproductuploader\Api\Data;

/**
 * Cminds Supplierfrontendproductuploader Supplier Configuration interface.
 * @api
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 */

interface SupplierConfigurationInterface
{
    /**
     * Entity data keys.
     */

    const PARENT_SKU = 'parent_sku';
    const NAME = 'name';
    const QTY = 'qty';
    const WEIGHT = 'weight';
    const ATTRIBUTES = 'attributes';

    /**
     * Get parent sku.
     *
     * @return string
     */
    public function getSku();

    /**
     * Set parent sku.
     *
     * @param string $sku
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierConfigurationInterface
     */
    public function setSku($sku);

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierConfigurationInterface
     */
    public function setName($name);

    /**
     * Get qty.
     *
     * @return string
     */
    public function getQty();

    /**
     * Set qty.
     *
     * @param string $qty
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierConfigurationInterface
     */
    public function setQty($qty);

    /**
     * Product weight
     *
     * @return float|null
     */
    public function getWeight();

    /**
     * Set product weight
     *
     * @param float $weight
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierConfigurationInterface
     */
    public function setWeight($weight);

    /**
     * Product custom attributes values.
     *
     * @return \Magento\Framework\Api\AttributeInterface[]|null
     */
    public function getAttributes();

    /**
     * Set product custom attributes
     *
     * @param \Magento\Framework\Api\AttributeInterface[] $attributes
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierConfigurationInterface
     */
    public function setAttributes(array $attributes = null );
}
