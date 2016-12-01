<?php

namespace FME\Pricecalculator\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface {

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

     /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $attributes = [];

        $attributes['pricing_rule'] = [
            'label' => 'Pricing Rule',
            'input' => 'text',
            'type' => 'varchar',
            'class' => '',
            'global' => true,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 'discount=0,0;size=0,0;area;fixed',
            'apply_to' => 'simple,configurable,bundle,grouped',
            'visible_on_front' => true,
            'is_configurable' => false,
            'wysiwyg_enabled' => false,
            'used_in_product_listing' => true,
            'is_html_allowed_on_front' => true,
            'group' => 'Price Calculator',
            'sort_order' => '83',
            'note' => "Default: discount=0,0;size=0,0;area;fixed <br>For area & discount in percentage: discount=10,50;size=15,30;area;percent
                <br>For volume & discount in fixed: discount=10,50;size=15,30;volume;fixed",
        ];

        $attributes['pricing_limit'] = [
            'label' => 'Pricing Limit',
            'input' => 'text',
            'type' => 'varchar',
            'class' => '',
            'global' => true,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 'Width_min=0;Width_max=0;Height_min=0;Height_max=0',
            'apply_to' => 'simple,configurable,bundle,grouped',
            'visible_on_front' => true,
            'is_configurable' => false,
            'wysiwyg_enabled' => false,
            'used_in_product_listing' => true,
            'is_html_allowed_on_front' => true,
            'group' => 'Price Calculator',
            'sort_order' => '84',
            'note' => "Default: Width_min=0;Width_max=0;Height_min=0;Height_max=0"
                . "<br>For applying limit on custom options: Width_min=24;Width_max=1800;Height_min=50;Height_max=100"
                . "<br>Please use keyword given in configuration like keyword_min or keyword_max",
        ];

        $attributes['current_unit'] = [
            'label' => 'Input Unit',
            'input' => 'select',
            'type' => 'varchar',
            'class' => '',
            'global' => true,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 'Inch',
            'apply_to' => 'simple,configurable,bundle,grouped',
            'option' => [
                'value' => [
                    'optionone' => ['Milli-Meter'],
                    'optiontwo' => ['Centi-Meter'],
                    'optionthree' => ['Meter'],
                    'optionfour' => ['Inch'],
                    'optionfive' => ['Foot'],
                ]
            ],
            'visible_on_front' => true,
            'is_configurable' => false,
            'wysiwyg_enabled' => false,
            'used_in_product_listing' => true,
            'is_html_allowed_on_front' => true,
            'group' => 'Price Calculator',
            'sort_order' => '85',
            'note' => "This is input unit of measurement.",
        ];

        $attributes['output_unit'] = [
            'label' => 'Output Unit',
            'input' => 'select',
            'type' => 'varchar',
            'class' => '',
            'global' => true,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'apply_to' => 'simple,configurable,bundle,grouped',
            'option' => [
                'value' => [
                    'optionone' => ['Milli-Meter'],
                    'optiontwo' => ['Centi-Meter'],
                    'optionthree' => ['Meter'],
                    'optionfour' => ['Inch'],
                    'optionfive' => ['Foot'],
                ]
            ],
            'visible_on_front' => true,
            'is_configurable' => false,
            'wysiwyg_enabled' => false,
            'used_in_product_listing' => true,
            'is_html_allowed_on_front' => true,
            'group' => 'Price Calculator',
            'sort_order' => '86',
            'note' => "This is output unit of measurement.",
        ];

        $attributes['price_unit_area'] = [
            'label' => 'Price Per Unit Area',
            'input' => 'text',
            'type' => 'varchar',
            'class' => 'validate-number',
            'global' => true,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'apply_to' => 'simple,configurable,bundle,grouped',
            'visible_on_front' => true,
            'is_configurable' => false,
            'wysiwyg_enabled' => false,
            'used_in_product_listing' => true,
            'is_html_allowed_on_front' => true,
            'group' => 'Price Calculator',
            'sort_order' => '87',
            'note' => 'Enter value greater then zero',
        ];

//        $attributes['errormessage'] = [
//            'label' => 'Error Message',
//            'input' => 'text',
//            'type' => 'varchar',
//            'class' => '',
//            'global' => true,
//            'visible' => true,
//            'required' => false,
//            'user_defined' => false,
//            'default' => '',
//            'apply_to' => 'simple,configurable,bundle,grouped',
//            'visible_on_front' => true,
//            'is_configurable' => false,
//            'wysiwyg_enabled' => false,
//            'used_in_product_listing' => true,
//            'is_html_allowed_on_front' => true,
//            'group' => 'Price Calculator',
//            'sort_order' => '88',
//            'note' => 'e.g "min=20 and max=40."',
//        ];

        /**
         * Add attributes to the eav/attribute
         */
        foreach ($attributes as $attribute => $options) {
            $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, $attribute, $options);
        }

//        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'test_attribute', [
//            'type' => 'int',
//            'backend' => '',
//            'frontend' => '',
//            'label' => 'Test Attribute',
//            'input' => '',
//            'class' => '',
//            'source' => '',
//            'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
//            'visible' => true,
//            'required' => false,
//            'user_defined' => false,
//            'default' => 0,
//            'searchable' => false,
//            'filterable' => false,
//            'comparable' => false,
//            'visible_on_front' => false,
//            'used_in_product_listing' => true,
//            'unique' => false,
//            'apply_to' => ''
//        ]);
    }

}
