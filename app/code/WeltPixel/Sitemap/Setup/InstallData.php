<?php

namespace WeltPixel\Sitemap\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $catalogSetupFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory,
                                ProductCollectionFactory $productCollectionFactory,
                                CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->catalogSetupFactory = $categorySetupFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $attributeName = 'weltpixel_exclude_from_sitemap';
        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);

        $catalogSetup->addAttribute(Category::ENTITY, $attributeName, [
            'type' => 'int',
            'label' => 'Exclude from Sitemap',
            'input' => 'select',
            'required' => false,
            'sort_order' => 10,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
        ]);

        $catalogSetup->addAttribute(Product::ENTITY, $attributeName, [
            'type' => 'int',
            'label' => 'Exclude from Sitemap',
            'input' => 'select',
            'required' => false,
            'sort_order' => 10,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
        ]);

        /**
         * Add default (0) attribute values for existing products, categories
         */
        $categoryAttribute = $catalogSetup->getAttribute(Category::ENTITY, $attributeName);
        $productAttribute = $catalogSetup->getAttribute(Product::ENTITY, $attributeName);

        $categoryAttributeId = $categoryAttribute['attribute_id'];
        $productAttributeId = $productAttribute['attribute_id'];

        $categoryEntityIntTable = $setup->getTable('catalog_category_entity_int');
        $productEntityIntTable = $setup->getTable('catalog_product_entity_int');

        /** Enterprise version fix, entity_id column changed to row_id */
        $categoryEntityId = 'entity_id';
        $categoryEntityIntColumns = array_keys($setup->getConnection()->describeTable($categoryEntityIntTable));
        if (in_array('row_id', $categoryEntityIntColumns)) {
            $categoryEntityId = 'row_id';
        }

        $productEntityId = 'entity_id';
        $productEntityIntColumns = array_keys($setup->getConnection()->describeTable($productEntityIntTable));
        if (in_array('row_id', $productEntityIntColumns)) {
            $productEntityId = 'row_id';
        }
        /** Enterprise version fix, entity_id column changed to row_id */

        $productCollection = $this->productCollectionFactory->create();
        $productIds = $productCollection->getAllIds();

        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryIds = $categoryCollection->getAllIds();

        foreach ($categoryIds as $id) {
            try {
                $setup->getConnection()->query(
                    "INSERT INTO `$categoryEntityIntTable`" .
                    "(`value_id`, `attribute_id`, `store_id`, `" . $categoryEntityId . "`, `value`) " .
                    "VALUES (NULL, '$categoryAttributeId', '0', '$id', '0');");
            } catch (\Exception $ex) {}
        }

        foreach ($productIds as $id) {
            try {
                $setup->getConnection()->query(
                    "INSERT INTO `$productEntityIntTable`" .
                    "(`value_id`, `attribute_id`, `store_id`, `" . $productEntityId . "`, `value`) " .
                    "VALUES (NULL, '$productAttributeId', '0', '$id', '0');");
            } catch (\Exception $ex) {}
        }
    }
}