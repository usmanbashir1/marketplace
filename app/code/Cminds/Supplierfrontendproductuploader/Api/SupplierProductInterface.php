<?php
namespace Cminds\Supplierfrontendproductuploader\Api;

/**
 * @api
 */
interface SupplierProductInterface
{
    /**
     * Retrieve list of Attribute Sets
     *
     * @param string $userAccessToken
     * @return \Magento\Eav\Api\Data\AttributeSetSearchResultsInterface
     */
    public function getAttributeSetList($userAccessToken);

    /**
     * Retrieve list of Attribute Sets
     *
     * @param string $userAccessToken
     * @param int $attributeSetId
     * @return \Magento\Eav\Api\Data\AttributeSearchResultsInterface
     */
    public function getAttributesList($userAccessToken, $attributeSetId);

    /**
     * Retrieve category list
     *
     * @param string $userAccessToken
     * @return \Magento\Catalog\Api\Data\CategorySearchResultsInterface
     */
    public function getCategoryList($userAccessToken);

    /**
     * Create product
     *
     * @param string $userAccessToken
     * @param \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierProductInterface[] $products
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\ResultInterface[]
     */
    public function saveProducts($userAccessToken, array $products);

    /**
     * Create simple product and assign to a configurable
     *
     * @param string $userAccessToken
     * @param \Cminds\Supplierfrontendproductuploader\Api\Data\SupplierConfigurationInterface[] $products
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\ResultInterface[]
     */
    public function createConfiguration($userAccessToken, array $products);

    /**
     * @param string $userAccessToken
     * @param string[] $productSkuArray
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\ResultInterface[]
     */
    public function deleteProducts($userAccessToken, array $productSkuArray);

}
