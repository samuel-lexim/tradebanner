<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param IndexerRegistry $indexerRegistry
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        IndexerRegistry $indexerRegistry,
        \Magento\Eav\Model\Config $eavConfig,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
        $state->setAreaCode('adminhtml');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        
        /*$setup->getConnection()
            ->addColumn($setup->getTable('customer_entity'),'rewardpoints_referrer', 
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => null],
                'Reward Points Referrer ID'
            );
        */
        
        
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            
            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute(Customer::ENTITY, 'rewardpoints_referrer', [
                'type' => 'int',
                'label' => 'Reward Points Referrer ID',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                "used_for_customer_segment" => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                "unique"     => false,
                'sort_order' => 999,
                'position' => 150,
                'system' => false,
                //"note"       => ""
            ]);
            //add attribute to attribute set
            
            $used_in_forms[] = "adminhtml_customer";
            $used_in_forms[] = "checkout_register";
            $used_in_forms[] = "customer_account_create";
            $used_in_forms[] = "customer_account_edit";
            $used_in_forms[] = "adminhtml_checkout";
            
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'rewardpoints_referrer');
            $attribute->addData([
                    'is_used_for_customer_segment' => true,
                    'is_system' => 0,
                    'is_user_defined' => 1,
                    'is_visible' => 1,
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    //'used_in_forms' => ['adminhtml_customer'],
                    'used_in_forms' => $used_in_forms,
                ]);

            $attribute->save();
            
            
            
            
            /************/
            
            
            /*$attributesInfo = [
                'rewardpoints_referrer' =>
                    [
                        'type' => 'int',
                        'label' => 'Reward Points Referrer ID',
                        'position' => 150,
                        'required' => false,
                        'visible'  =>true,
                        'system'   => 0,
                        'user_defined' => true
                    ],
            ];

            foreach ($attributesInfo as $attributeCode => $attributeParams) {
                $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $attributeCode, $attributeParams);
            }

            $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);
            $attributeSetId   = $customerSetup->getDefaultAttributeSetId($entityTypeId);
            $attributeGroupId = $customerSetup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

            $customerSetup->addAttributeToGroup($entityTypeId,
                $attributeSetId,
                $attributeGroupId,
                'rewardpoints_referrer',
                '999'  //sort_order
            );
            
            $entities = $customerSetup->getDefaultEntities();
            foreach ($entities as $entityName => $entity) {
                $customerSetup->addEntityType($entityName, $entity);
            }*/
        }
        
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            
            //add attribute to attribute set
            $used_in_forms = [];
            $used_in_forms[] = "adminhtml_customer";
            $used_in_forms[] = "checkout_register";
            $used_in_forms[] = "customer_account_create";
            $used_in_forms[] = "customer_account_edit";
            $used_in_forms[] = "adminhtml_checkout";
            
            
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'rewardpoints_accumulated');
            $attribute->addData([
                'is_used_for_customer_segment' => true,
                'is_system' => 0,
                'is_user_defined' => 0,
                'is_visible' => 0,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $used_in_forms,
            ]);

            $attribute->save();
            
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'rewardpoints_available');
            $attribute->addData([
                'is_used_for_customer_segment' => true,
                'is_system' => 0,
                'is_user_defined' => 0,
                'is_visible' => 0,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $used_in_forms,
            ]);

            $attribute->save();
            
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'rewardpoints_spent');
            $attribute->addData([
                'is_used_for_customer_segment' => true,
                'is_system' => 0,
                'is_user_defined' => 0,
                'is_visible' => 0,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $used_in_forms,
            ]);

            $attribute->save();
            
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'rewardpoints_lost');
            $attribute->addData([
                'is_used_for_customer_segment' => true,
                'is_system' => 0,
                'is_user_defined' => 0,
                'is_visible' => 0,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $used_in_forms,
            ]);

            $attribute->save();
            
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'rewardpoints_waiting');
            $attribute->addData([
                'is_used_for_customer_segment' => true,
                'is_system' => 0,
                'is_user_defined' => 0,
                'is_visible' => 0,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $used_in_forms,
            ]);

            $attribute->save();
            
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'rewardpoints_not_available');
            $attribute->addData([
                'is_used_for_customer_segment' => true,
                'is_system' => 0,
                'is_user_defined' => 0,
                'is_visible' => 0,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $used_in_forms,
            ]);

            $attribute->save();
        }
        
        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            
            /*$setup->getConnection()->update(
                $setup->getTable('rewardpoints_account'),
                array('period' => 'date_insertion'));
            */
            $query = "UPDATE {$setup->getTable('rewardpoints_account')} SET period = date_insertion";
            $setup->getConnection()->query($query);
        }

        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->reindexAll();
        $this->eavConfig->clear();
        $setup->endSetup();
    }
}

