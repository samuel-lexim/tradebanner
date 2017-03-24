<?php
/**
 * Copyright © 2015 J2T DESIGN. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        /**
		* Create table 'rewardpoints_account'
		*/
	   $table = $installer->getConnection()->newTable(
		   $installer->getTable('rewardpoints_account')
	   )->addColumn(
		   'rewardpoints_account_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
		   'Entity Id'
	   )->addColumn(
		   'customer_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => true, 'nullable' => false, 'default' => '0'],
		   'Customer Id'
	   )->addColumn(
		   'store_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Store Id'
	   )->addColumn(
		   'order_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   60,
		   [],
		   'Order Id'
	   )->addColumn(
		   'points_current',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Points Gathered'
	   )->addColumn(
		   'points_spent',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Points Spent'
	   )->addColumn(
		   'rewardpoints_description',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Point Description'
	   )->addColumn(
		   'rewardpoints_linker',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['unsigned' => true, 'default' => '0'],
		   'Linker'
	   )->addColumn(
		   'date_start',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		   null,
		   ['nullable' => true],
		   'Start Date'
	   )->addColumn(
		   'date_end',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		   null,
		   ['nullable' => true],
		   'End Date'
	   )->addColumn(
		   'convertion_rate',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['unsigned' => true, 'nullable' => false, 'default' => '1'],
		   'Conversion Rate'
	   )->addColumn(
		   'rewardpoints_referral_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['unsigned' => true, 'nullable' => false, 'default' => '0'],
		   'Referral ID'
	   )->addColumn(
		   'rewardpoints_status',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Status'
	   )->addColumn(
		   'rewardpoints_state',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'State'
	   )->addColumn(
		   'date_order',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
		   null,
		   ['nullable' => true],
		   'Date Order'
	   )->addColumn(
		   'date_insertion',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
		   null,
		   ['nullable' => true],
		   'Date Insertion'
	   )->addColumn(
		   'Period',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		   null,
		   ['nullable' => true],
		   'Period'
	   )->addColumn(
		   'object_name',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Object Name'
	   )->addColumn(
		   'quote_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['unsigned' => true, 'nullable' => true],
		   'Quote ID'
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_account', ['store_id']),
		   ['store_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_account', ['rewardpoints_status']),
		   ['rewardpoints_status']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_account', ['rewardpoints_state']),
		   ['rewardpoints_state']
	   )->setComment(
		   'Reward Points'
	   );
	   $installer->getConnection()->createTable($table);


	   /**
		* Create table 'rewardpoints_catalogrules'
		*/
	   $table = $installer->getConnection()->newTable(
		   $installer->getTable('rewardpoints_catalogrules')
	   )->addColumn(
		   'rule_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
		   'Entity Id'
	   )->addColumn(
		   'title',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   166,
		   [],
		   'Title'
	   )->addColumn(
		   'status',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => '0'],
		   'Status'
	   )->addColumn(
		   'website_ids',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Website Ids'
	   )->addColumn(
		   'customer_group_ids',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Customer Group Ids'
	   )->addColumn(
		   'action_type',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => '0'],
		   'Action Type'
	   )->addColumn(
		   'conditions_serialized',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   null,
		   [],
		   'Condition'
	   )->addColumn(
		   'from_date',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		   null,
		   ['nullable' => true],
		   'From Date'
	   )->addColumn(
		   'to_date',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		   null,
		   ['nullable' => true],
		   'To Date'
	   )->addColumn(
		   'labels',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   null,
		   [],
		   'Labels'
	   )->addColumn(
		   'labels_summary',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   null,
		   [],
		   'Labels Summary'
	   )->addColumn(
		   'sort_order',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => '0'],
		   'Sort Order'
	   )->addColumn(
		   'points',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Points'
	   )->addColumn(
		   'rule_type',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => '0'],
		   'Rule Type'
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_catalogrules', ['rule_id']),
		   ['rule_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_catalogrules', ['website_ids']),
		   ['website_ids']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_catalogrules', ['customer_group_ids']),
		   ['customer_group_ids']
	   )->setComment(
		   'Reward Points Catalog Rules'
	   );
	   $installer->getConnection()->createTable($table);

	   /**
		* Create table 'rewardpoints_pointrules'
		*/
	   $table = $installer->getConnection()->newTable(
		   $installer->getTable('rewardpoints_pointrules')
	   )->addColumn(
		   'rule_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
		   'Entity Id'
	   )->addColumn(
		   'title',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   166,
		   [],
		   'Title'
	   )->addColumn(
		   'status',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => '0'],
		   'Status'
	   )->addColumn(
		   'website_ids',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Website Ids'
	   )->addColumn(
		   'customer_group_ids',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Customer Group Ids'
	   )->addColumn(
		   'action_type',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => '0'],
		   'Action Type'
	   )->addColumn(
		   'conditions_serialized',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   null,
		   [],
		   'Condition'
	   )->addColumn(
		   'from_date',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		   null,
		   ['nullable' => true],
		   'From Date'
	   )->addColumn(
		   'to_date',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		   null,
		   ['nullable' => true],
		   'To Date'
	   )->addColumn(
		   'labels',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   null,
		   [],
		   'Labels'
	   )->addColumn(
		   'labels_summary',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   null,
		   [],
		   'Labels Summary'
	   )->addColumn(
		   'sort_order',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => '0'],
		   'Sort Order'
	   )->addColumn(
		   'points',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Points'
	   )->addColumn(
		   'rule_type',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => '0'],
		   'Rule Type'
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_pointrules', ['rule_id']),
		   ['rule_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_pointrules', ['website_ids']),
		   ['website_ids']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_pointrules', ['customer_group_ids']),
		   ['customer_group_ids']
	   )->setComment(
		   'Reward Points Store Rules'
	   );
	   $installer->getConnection()->createTable($table);

	   /**
		* Create table 'rewardpoints_pointrules'
		*/
	   $table = $installer->getConnection()->newTable(
		   $installer->getTable('rewardpoints_flat_account')
	   )->addColumn(
		   'flat_account_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
		   'Entity Id'
	   )->addColumn(
		   'user_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => true, 'nullable' => false, 'default' => '0'],
		   'Customer Id'
	   )->addColumn(
		   'store_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => true, 'nullable' => false, 'default' => '0'],
		   'Store Id'
	   )->addColumn(
		   'points_collected',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Collected Points'
	   )->addColumn(
		   'points_used',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Used Points'
	   )->addColumn(
		   'points_waiting',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Waiting Points'
	   )->addColumn(
		   'points_not_available',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Not Available Points'
	   )->addColumn(
		   'points_current',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Current Points'
	   )->addColumn(
		   'points_lost',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Lost Points'
	   )->addColumn(
		   'notification_qty',
		   \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
		   null,
		   ['nullable' => true, 'default' => '0'],
		   'Notification Qty'
	   )->addColumn(
		   'notification_date',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
		   null,
		   ['nullable' => true],
		   'Notification Date'
	   )->addColumn(
		   'last_check',
		   \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		   null,
		   ['nullable' => true],
		   'Last Verification Date'
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_flat_account', ['flat_account_id']),
		   ['flat_account_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_flat_account', ['store_id']),
		   ['store_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_flat_account', ['user_id']),
		   ['user_id']
	   )->setComment(
		   'Reward Points Flat Account'
	   );
	   $installer->getConnection()->createTable($table);


	   /**
		* Create table 'rewardpoints_referral'
		*/
	   $table = $installer->getConnection()->newTable(
		   $installer->getTable('rewardpoints_referral')
	   )->addColumn(
		   'rewardpoints_referral_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
		   'Entity Id'
	   )->addColumn(
		   'rewardpoints_referral_parent_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => true, 'nullable' => false, 'default' => '0'],
		   'Parent Customer Id'
	   )->addColumn(
		   'rewardpoints_referral_child_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => true, 'nullable' => false, 'default' => '0'],
		   'Child Customer Id'
	   )->addColumn(
		   'rewardpoints_referral_email',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Referral Email Address'
	   )->addColumn(
		   'rewardpoints_referral_name',
		   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		   255,
		   [],
		   'Referral Name'
	   )->addColumn(
		   'rewardpoints_referral_status',
		   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		   null,
		   ['unsigned' => false, 'nullable' => false, 'default' => 0],
		   'Status'
	   )       
           ->addColumn(
		   'store_id',
		   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		   null,
		   ['unsigned' => true, 'nullable' => true],
		   'Store ID'
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_referral', ['store_id']),
		   ['store_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_referral', ['rewardpoints_referral_id']),
		   ['rewardpoints_referral_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_referral', ['rewardpoints_referral_parent_id']),
		   ['rewardpoints_referral_parent_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_referral', ['rewardpoints_referral_child_id']),
		   ['rewardpoints_referral_child_id']
	   )->addIndex(
		   $installer->getIdxName('rewardpoints_referral', ['rewardpoints_referral_email']),
		   ['rewardpoints_referral_email']
	   )->setComment(
		   'Reward Points Referral'
	   );
	   $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}
