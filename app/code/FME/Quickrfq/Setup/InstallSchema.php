<?php

namespace FME\Quickrfq\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{


    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {


        $installer = $setup;
        $installer->startSetup();


        /**
         * Create table 'quickrfq'
         */

        $table = $installer->getConnection()->newTable($installer->getTable('fme_quickrfq'))
            ->addColumn('quickrfq_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Quickrfq ID'
            )
            ->addColumn('company',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Company'
            )
            ->addColumn('contact_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Contact Name'
            )
            ->addColumn('email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Email'
            )
            ->addColumn('category',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'Banners'],
                'Category'
            )
            ->addColumn('material_01',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material 1'
            )
            ->addColumn('material_02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material 2'
            )
            ->addColumn('material_03',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material 3'
            )
            ->addColumn('material_04',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material 4'
            )
            ->addColumn('material_05',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material 5'
            )
            ->addColumn('material_06',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material 6'
            )
            ->addColumn('standtype_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Stand Type'
            )
            ->addColumn('color_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Color'
            )
            ->addColumn('color_options_02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Color 2'
            )
            ->addColumn('finishing_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Finishing Options'
            )
            ->addColumn('note_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Option/Note'
            )
            ->addColumn('diecut_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Die Cut'
            )
            ->addColumn('finishing_options_mc01',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Finishing Options Multi 1'
            )
            ->addColumn('finishing_options_mc02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Finishing Options Multi 2'
            )
            ->addColumn('finishing_options_mc03',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Finishing Options Multi 3'
            )
            ->addColumn('finishing_options_mc04',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Finishing Options Multi 4'
            )
            ->addColumn('frame_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Frame'
            )
            ->addColumn('grommet_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Gromets'
            )
            ->addColumn('hstake_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'H-Stakes'
            )
            ->addColumn('lamination_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Lamination'
            )
            ->addColumn('lamination_options_mc01',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Lamination Multi Choice 1'
            )
            ->addColumn('lamination_options_mc02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Lamination Multi Choice 2'
            )
            ->addColumn('lamination_options_mc03',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Lamination Multi Choice 3'
            )
            ->addColumn('round_corners_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Round Corners'
            )
            ->addColumn('thickness_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Thickness'
            )
            ->addColumn('thickness_options_02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Thickness 2'
            )
            ->addColumn('thickness_options_03',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Thickness 3'
            )
            ->addColumn('thickness_options_04',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Thickness 4'
            )
            ->addColumn('size_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Size'
            )
            ->addColumn('size_options_02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Size 2'
            )
            ->addColumn('product_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Product'
            )
            ->addColumn('carmodel',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Car Model'
            )
            ->addColumn('carwrapping',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Car Wrapping'
            )
            ->addColumn('date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                [],
                'Date'
            )
            ->addColumn('budget',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Budget'
            )
            ->addColumn('overview',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Overview'
            )
            ->addColumn('width',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Width'
            )
            ->addColumn('height',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Height'
            )
            ->addColumn('quantity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Quantity'
            )
            ->addColumn('delivery',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'Pick-Up'],
                'Delivery'
            )
            ->addColumn('company_name_02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Company-Name'
            )
            ->addColumn('first_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'First Name'
            )
            ->addColumn('last_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Last Name'
            )
            ->addColumn('street_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Street Address'
            )
            ->addColumn('address_line_02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Address Line 2'
            )
            ->addColumn('city',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'City'
            )
            ->addColumn('state_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'State'
            )
            ->addColumn('zip_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Zip Code'
            )
            ->addColumn('windholes',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Windholes'
            )
            ->addColumn('hemming',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Hemming'
            )
            ->addColumn('grommets',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Grommets'
            )
            ->addColumn('prd',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Prd'
            )
            ->addColumn('status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'New'],
                'Status'
            )
            ->addColumn('create_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Date'
            )
            ->addColumn('update_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update Date'
            );


        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }

}