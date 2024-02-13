<?php

namespace Customer\Attr\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;

class InstallData implements InstallDataInterface
{
    /**
    * @var CustomerSetup
    */

    private $customerSetupFactory;
    /**
     * @var SetFactory
     */
    private $attributeSetFactory;

   public function __construct(
   		ModuleDataSetupInterface $moduleDataSetup,
    	CustomerSetupFactory $customerSetupFactory,
    	SetFactory $attributeSetFactory

   ){
        $this->moduleDataSetup = $moduleDataSetup;
    	$this->customerSetupFactory = $customerSetupFactory;
    	$this->attributeSetFactory = $attributeSetFactory;
   }
   public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
   {    
	    $this->moduleDataSetup->getConnection()->startSetup();
	    /** @var CustomerSetup $customerSetup */
	    $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
	    $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
	    $attributeSetId = $customerEntity->getDefaultAttributeSetId();

	    /** @var $attributeSet Set */
	    $attributeSet = $this->attributeSetFactory->create();
	    $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

	    $customerSetup->addAttribute(
	        Customer::ENTITY,
	        'customer_product_response',
	        [
	            'label' => 'Customer Product Response',
	            'input' => 'text',
	            'type' => 'varchar',
	            'source' => '',
	            'required' => false,
	            'position' => 999,
	            'visible' => false,
	            'system' => false,
	            'is_used_in_grid' => false,
	            'is_visible_in_grid' => false,
	            'is_filterable_in_grid' => false,
	            'is_searchable_in_grid' => false,
	            'backend' => ''
	        ]
	    );

	    $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_product_response');
	    /*$attribute->addData([
	        'used_in_forms' => [
	            'adminhtml_customer'
	        ]
	    ]);*/
	    $attribute->addData([
	        'attribute_set_id' => $attributeSetId,
	        'attribute_group_id' => $attributeGroupId

	    ]);
	    $attribute->save();

	    $this->moduleDataSetup->getConnection()->endSetup();

   }
}