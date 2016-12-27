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
            ->addColumn('material',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material'
            )
            ->addColumn('material_02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material'
            )
            ->addColumn('material_03',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material'
            )
            ->addColumn('material_04',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material'
            )
            ->addColumn('material_05',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material'
            )
            ->addColumn('material_06',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Material'
            )
            ->addColumn('color_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Color'
            )
            ->addColumn('diecut_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Die Cut'
            )
            ->addColumn('finishing_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Finish Options'
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
            ->addColumn('lamination_options_02',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Lamination'
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
                'Thickness'
            )
            ->addColumn('thickness_options_03',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Thickness'
            )
            ->addColumn('thickness_options_04',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Thickness'
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
                'Size'
            )
            ->addColumn('standtype_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Stand Type'
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
            ->addColumn('lamination',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'N/A'],
                'Lamination'
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

