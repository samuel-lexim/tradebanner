<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Resource\Referral;

/**
 * Quotes collection
 *
 * @author      J2T Design Team <contact@j2t-design.net>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $_customerTable;
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('J2t\Rewardpoints\Model\Referral', 'J2t\Rewardpoints\Model\Resource\Referral');
        $this->_customerTable = $this->getTable('customer_entity');
        //customer_entity
    }
    
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            ['cust' => $this->_customerTable],
            'rewardpoints_referral_parent_id = cust.entity_id',
            ['*']
        );
    }
    
    public function setStoreFilter($storeId)
    {
        $this->addFieldToFilter('main_table.store_id', $storeId);
        return $this;
    }
    
    public function addEmailFilter($email)
    {
        $this->addFieldToFilter('rewardpoints_referral_email', $email);
        return $this;
    }
    
    public function addFlagFilter($status)
    {
        $this->addFieldToFilter('rewardpoints_referral_status', $status);
        return $this;
    }
    
    public function addClientFilter($id)
    {
        $this->addFieldToFilter('rewardpoints_referral_parent_id', $id);
        return $this;
    }
    
}
