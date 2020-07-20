<?php
/**
 * Cminds SupplierRedirection
 *
 * @category Cminds
 * @package  Cminds_SupplierRedirection
 */
namespace Cminds\SupplierRedirection\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Cminds SupplierRedirection Install Script.
 *
 * @category Cminds
 * @package  Cminds_SupplierRedirection
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;
    
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
    
    /**
     * Object constructor.
     *
     * @param CustomerSetupFactory  $customerSetupFactory   Customer setup factory object.
     * @param AttributeSetFactory   $attributeSetFactory    Attribute Set Factory Object
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @param ModuleDataSetupInterface $setup   Module data setup object.
     * @param ModuleContextInterface   $context Module context object.
     *
     * @return void
     * @throws \Exception
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
         
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
         
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'domain_url',
            [
                'type' => 'varchar',
                'label' => 'Supplier URL',
                'input' => 'text',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => 0,
                'position' => 0,
            ]
        );
        $customerSetup->getEavConfig()
            ->getAttribute(
                'customer',
                'domain_url'
            )
            ->setData(
                'used_in_forms',
                ['adminhtml_customer']
            )
            ->setData(
                'attribute_set_id',
                $attributeSetId
            )
            ->setData(
                'attribute_group_id',
                $attributeGroupId
            )
            ->save();

        $setup->endSetup();
    }    
}
