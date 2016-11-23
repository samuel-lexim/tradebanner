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
                ['nullable' => false, 'default' => 'Backlit Film'],
                'Material'
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

