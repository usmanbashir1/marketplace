<?php
namespace Cminds\Supplierfrontendproductuploader\Api\Data;

/**
 * Cminds Supplierfrontendproductuploader Sources interface.
 * @api
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 */

interface SupplierProductInterface
{
    /**
     * Entity data keys.
     */

//    required values
    const SKU = 'sku';
    const QTY = 'qty';
    const NAME = 'name';
    const PRICE = 'price';
    const TYPE_ID = 'type_id';
    const CATEGORIES = 'categories';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';

//    optional
    const DESCRIPTION = 'description';
    const SHORT_DESCRIPTION = 'short_description';
    const SPECIAL_PRICE = 'special_price';
    const SPECIAL_PRICE_FROM = 'special_from_date';
    const SPECIAL_PRICE_TO = 'special_to_date';
    const WEIGHT = 'weight';
    const MEDIA_GALLERY = 'media_gallery'; // there is a media gallery product attribute, but this one is different
    const VARIATION_ATTRIBUTES = 'variation_attributes';
    const CUSTOM_ATTRIBUTES = 'custom_attributes';

    const MANDATORY_ATTRIBUTES = [
        self::SKU,
        self::QTY,
        self::NAME,
        self::PRICE,
        self::TYPE_ID,
        self::CATEGORIES,
        self::ATTRIBUTE_SET_ID
    ];

    const OPTIONAL_ATTRIBUTES = [
        self::DESCRIPTION,
        self::SHORT_DESCRIPTION,
        self::SPECIAL_PRICE,
        self::SPECIAL_PRICE_FROM,
        self::SPECIAL_PRICE_TO,
        self::WEIGHT,
        self::MEDIA_GALLERY,
        self::VARIATION_ATTRIBUTES
    ];

    /**
     * Get sku.
     *
     * @return string
     */
    public function getSku();

    /**
     * Set sku.
     *
     * @param string $sku
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
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
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setName($name);

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setDescription($description);

    /**
     * Get short description.
     *
     * @return string
     */
    public function getShortDescription();

    /**
     * Set short description.
     *
     * @param string $shortDescription
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setShortDescription($shortDescription);

    /**
     * Get price.
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Set price.
     *
     * @param float $price
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setPrice($price);

    /**
     * Get special price.
     *
     * @return float|null
     */
    public function getSpecialPrice();

    /**
     * Set special price.
     *
     * @param float $price
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setSpecialPrice($price = null);

    /**
     * Get special price from date.
     *
     * @return string|null
     */
    public function getSpecialPriceFrom();

    /**
     * Set special price from date.
     *
     * @param string|null $price
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setSpecialPriceFrom($date = null);

    /**
     * Get special price to date.
     *
     * @return string|null
     */
    public function getSpecialPriceTo();

    /**
     * Set special price to date.
     *
     * @param string|null $specialPriceTo
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setSpecialPriceTo($date = null);

    /**
     * Product qty.
     *
     * @return int
     */
    public function getQty();

    /**
     * Set product qty.
     *
     * @param int $qty
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
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
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setWeight($weight = null);

    /**
     * Product attribute set id
     *
     * @return int
     */
    public function getAttributeSetId();

    /**
     * Set attribute set id
     *
     * @param int $setId
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setAttributeSetId($setId);

    /**
     * Product type
     *
     * @return string
     */
    public function getTypeId();

    /**
     * Set product type
     *
     * @param string $type_id
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setTypeId($type_id);



    /**
     * Get variation attributes for configurable products.
     *
     * @return string[]|null
     */
    public function getVariationAttributes();

    /**
     * Set variation attributes.
     *
     * @param string[] $attributes
     *
     * @return string
     */
    public function setVariationAttributes( $attributes = null);

    /**
     * Product categories
     *
     * @return string[]
     */
    public function getCategories();

    /**
     * Set product categories
     *
     * @param string[] $categories
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setCategories(array $categories);

    /**
     * Product media gallery
     *
     * @return string[]|null
     */
    public function getMediaGallery();

    /**
     * Set product media gallery
     *
     * @param string[] $images
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setMediaGallery(array $images = null );

    /**
     * Product custom attributes
     *
     * @return \Magento\Framework\Api\AttributeInterface[]|null
     */
    public function getCustomAttributes();

    /**
     * Set product custom attributes
     *
     * @param \Magento\Framework\Api\AttributeInterface[] $attributes
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface
     */
    public function setCustomAttributes(array $attributes = null );
}
