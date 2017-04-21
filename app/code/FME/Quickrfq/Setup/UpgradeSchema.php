<?php

namespace FME\Quickrfq\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
        
        
        
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
                
                
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $installer = $setup;
            $installer->startSetup();
                        
                        
            /**
                                * update column 'overview'
                        */
                        
                    $table = $installer->getTable('fme_quickrfq');
                                               
                    $installer->getConnection()->modifyColumn(
                        $table,
                        'overview',
                        [
                                                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                                            'length' => '64k',
                                                            'nullable' => true,
                                                            'default' => null,
                                                            'comment' => 'Overview'
                                                    ]
                    );
                                       
                        
            $installer->endSetup();
        }
    }
}
