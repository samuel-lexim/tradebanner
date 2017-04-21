<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;

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
    protected $categorySetupFactory;

    /**
     * Quote setup factory
     *
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;
	
	private $customerSetupFactory;


    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $options = ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'visible' => false, 'required' => false];
        $options_smallint = ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 'visible' => false, 'required' => false];
        $options_text = ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'visible' => false, 'required' => false];

        //sales_invoice
        //$salesSetup = $installer->createSalesSetup(['resourceName' => 'sales_setup']);
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute('order', 'rewardpoints_description', $options_text);
        $salesSetup->addAttribute('order', 'rewardpoints_quantity', $options);
        $salesSetup->addAttribute('order', 'base_rewardpoints', $options);
        $salesSetup->addAttribute('order', 'rewardpoints', $options);
        $salesSetup->addAttribute('order', 'rewardpoints_referrer', $options_smallint);
        $salesSetup->addAttribute('order', 'rewardpoints_gathered', $options);

        $salesSetup->addAttribute('invoice', 'rewardpoints_description', $options_text);
        $salesSetup->addAttribute('invoice', 'rewardpoints_quantity', $options);
        $salesSetup->addAttribute('invoice', 'base_rewardpoints', $options);
        $salesSetup->addAttribute('invoice', 'rewardpoints', $options);
        $salesSetup->addAttribute('invoice', 'rewardpoints_referrer', $options_smallint);
        $salesSetup->addAttribute('invoice', 'rewardpoints_gathered', $options);

        //$quoteSetup = $installer->createQuoteSetup(['resourceName' => 'quote_setup']);
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute('quote', 'rewardpoints_description', $options_text);
        $quoteSetup->addAttribute('quote', 'rewardpoints_quantity', $options);
        $quoteSetup->addAttribute('quote', 'base_rewardpoints', $options);
        $quoteSetup->addAttribute('quote', 'rewardpoints', $options);
        $quoteSetup->addAttribute('quote', 'rewardpoints_referrer', $options_smallint);
        $quoteSetup->addAttribute('quote', 'rewardpoints_gathered', $options);

        //$quoteSetup = $installer->createQuoteSetup(['resourceName' => 'quote_setup']);
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute('quote_address', 'rewardpoints_description', $options_text);
        $quoteSetup->addAttribute('quote_address', 'rewardpoints_quantity', $options);
        $quoteSetup->addAttribute('quote_address', 'base_rewardpoints', $options);
        $quoteSetup->addAttribute('quote_address', 'rewardpoints', $options);
        $quoteSetup->addAttribute('quote_address', 'rewardpoints_referrer', $options_smallint);
        $quoteSetup->addAttribute('quote_address', 'rewardpoints_gathered', $options);

        //$quoteSetup->addAttribute('quote', 'rewardpoints_cart_rule_text', $options_text);

        $entities = ['quote', 'quote_item'];
        $entities_order = ['order', 'order_item', 'creditmemo', 'creditmemo_item'];
        foreach ($entities as $entity) {
            /*$installer->createQuoteSetup(
                    ['resourceName' => 'quote_setup']
            )*/
            $this->quoteSetupFactory->create(['setup' => $setup])->addAttribute($entity, 'rewardpoints_gathered', $options);
            /*$installer->createQuoteSetup(
                    ['resourceName' => 'quote_setup']
            )*/
            $this->quoteSetupFactory->create(['setup' => $setup])->addAttribute($entity, 'rewardpoints_used', $options);
            /*$installer->createQuoteSetup(
                    ['resourceName' => 'quote_setup']
            )*/
            $this->quoteSetupFactory->create(['setup' => $setup])->addAttribute($entity, 'rewardpoints_cart_rule_text', $options_text);

            $this->quoteSetupFactory->create(['setup' => $setup])->addAttribute($entity, 'reward_discount_amount', $options);
            $this->quoteSetupFactory->create(['setup' => $setup])->addAttribute($entity, 'base_reward_discount_amount', $options);
        }
        foreach ($entities_order as $entity) {
            /*$installer->createSalesSetup(
                    ['resourceName' => 'sales_setup']
            )*/
            $this->salesSetupFactory->create(['setup' => $setup])->addAttribute($entity, 'rewardpoints_gathered', $options);
            /*$installer->createSalesSetup(
                    ['resourceName' => 'sales_setup']
            )*/
            $this->salesSetupFactory->create(['setup' => $setup])->addAttribute($entity, 'rewardpoints_used', $options);
            /*$installer->createSalesSetup(
                    ['resourceName' => 'sales_setup']
            )*/
            $this->salesSetupFactory->create(['setup' => $setup])->addAttribute($entity, 'rewardpoints_cart_rule_text', $options_text);
        }


		/////////////////////
		
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();
		
        $attributesInfo = [
            'rewardpoints_accumulated' =>
                [
                    'type' => 'decimal',
                    'label' => 'Total Accumulated Points',
                    'position' => 40,
                    'required' => false,
                    'visible'  =>false
                ],
            'rewardpoints_available' =>
                [
                    'type' => 'decimal',
                    'label' => 'Total Available Points',
                    'position' => 50,
                    'required' => false,
                    'visible'  =>false
                ],
            'rewardpoints_spent' =>
                [
                    'type' => 'decimal',
                    'label' => 'Total Spent Points',
                    'position' => 50,
                    'required' => false,
                    'visible'  =>false
                ],
            'rewardpoints_lost' =>
                [
                    'type' => 'decimal',
                    'label' => 'Total Lost Points',
                    'position' => 50,
                    'required' => false,
                    'visible'  =>false
                ],
            'rewardpoints_waiting' =>
                [
                    'type' => 'decimal',
                    'label' => 'Total Waiting Points',
                    'position' => 50,
                    'required' => false,
                    'visible'  =>false
                ],
            'rewardpoints_not_available' =>
                [
                    'type' => 'decimal',
                    'label' => 'Total Not Available Points',
                    'position' => 60,
                    'required' => false,
                    'visible'  =>false
                ],
        ];
		
        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute('customer', $attributeCode, $attributeParams);
        }
		
        $setup->endSetup();
    }
}
