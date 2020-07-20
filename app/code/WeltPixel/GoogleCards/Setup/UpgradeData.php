<?php

namespace WeltPixel\GoogleCards\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 * @package WeltPixel\GoogleCards\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        if (version_compare($context->getVersion(), "1.0.5", "<")) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            if (!$eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'item_condition')) {
                $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'item_condition');
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'item_condition',
                    [
                        'type' => 'varchar',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Item Condition',
                        'input' => 'select',
                        'class' => '',
                        'source' => 'WeltPixel\GoogleCards\Model\Config\Source\ItemConditionsOptions',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => false,
                        'unique' => false
                    ]
                );
            }
        }
    }
}