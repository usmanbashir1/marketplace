<?php
namespace Cminds\Supplierfrontendproductuploader\Model\Data;

use Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\AttributeInterface;

/**
 * Cminds Supplierfrontendproductuploader SupplierProduct interface.
 *
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 */

class SupplierProduct extends AbstractSimpleObject implements SupplierProductInterface
{

    /**
     * {@inheritdoc}
     */
    public function getSku(){
        return $this->_get(self::SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku){
        $this->setData(self::SKU, $sku);
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
    public function getDescription(){
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description){
        $this->setData(self::DESCRIPTION, $description);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription(){
        return $this->_get(self::SHORT_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription($shortDescription){
        $this->setData(self::SHORT_DESCRIPTION, $shortDescription);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(){
        return $this->_get(self::PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price){
        $this->setData(self::PRICE, $price);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialPrice(){
        return $this->_get(self::SPECIAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpecialPrice($price = null){
        $this->setData(self::SPECIAL_PRICE, $price);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialPriceFrom(){
        return $this->_get(self::SPECIAL_PRICE_FROM);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpecialPriceFrom($date = null){
        $this->setData(self::SPECIAL_PRICE_FROM, $date);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialPriceTo(){
        return $this->_get(self::SPECIAL_PRICE_TO);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpecialPriceTo($date = null){
        $this->setData(self::SPECIAL_PRICE_TO, $date);
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
    public function setWeight($weight = null){
        $this->setData(self::WEIGHT, $weight);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSetId(){
        return $this->_get(self::ATTRIBUTE_SET_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeSetId($setId){
        $this->setData(self::ATTRIBUTE_SET_ID, $setId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId(){
        return $this->_get(self::TYPE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeId($productType){
        $this->setData(self::TYPE_ID, $productType);
        return $this;
    }




    /**
     * {@inheritdoc}
     */
    public function getVariationAttributes(){
        return $this->_get(self::VARIATION_ATTRIBUTES);
    }

    /**
     * {@inheritdoc}
     */
    public function setVariationAttributes($attributes = null){
        $this->setData(self::VARIATION_ATTRIBUTES, $attributes);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories(){
        return $this->_get(self::CATEGORIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategories(array $categories){
        $this->setData(self::CATEGORIES, $categories);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaGallery(){
        return $this->_get(self::MEDIA_GALLERY);
    }

    /**
     * {@inheritdoc}
     */
    public function setMediaGallery(array $images = null){
        $this->setData(self::MEDIA_GALLERY, $images);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributes(){
        return $this->_get(self::CUSTOM_ATTRIBUTES);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes = null){
        $this->setData(self::CUSTOM_ATTRIBUTES, $attributes);
        return $this;
    }


    /**
     * Get required attribute codes
     * @return array
     */
    public function toArray(){
       $arrayData = $this->__toArray();
       if(isset($arrayData[self::CUSTOM_ATTRIBUTES])){
           foreach ($arrayData[self::CUSTOM_ATTRIBUTES] as $key => $customAttribute){
                $arrayData[$customAttribute[AttributeInterface::ATTRIBUTE_CODE]] = $customAttribute[AttributeInterface::VALUE];
           }
           unset($arrayData[self::CUSTOM_ATTRIBUTES]);
       }
       return $arrayData;
    }
}
