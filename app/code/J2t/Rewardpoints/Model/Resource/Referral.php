<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Resource;

/**
 * AdminNotification Inbox model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Referral extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * AdminNotification Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('rewardpoints_referral', 'rewardpoints_referral_id');
    }
    
    public function loadByEmail(\J2t\Rewardpoints\Model\Referral $object, $customerEmail, $storeId = null)
    {
        $adapter = $this->getConnection();
        //$adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable()
        )->where(
            'rewardpoints_referral_email = ?', $customerEmail
        );
        
        if ($storeId){
            $select->where(
                    'main_table.store_id = ?', $storeId
            );
        }
        
        $select->limit(
            1
        );
        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }
        $this->_afterLoad($object);

        return $this;
    }
    
    
    public function loadByChildId(\J2t\Rewardpoints\Model\Point $object, $child_id)
    {
        $adapter = $this->getConnection();
        //$adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable()
        )->where('rewardpoints_referral_child_id = ?',$child_id);

        $select->limit(
            1
        );
        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }
        $this->_afterLoad($object);

        return $this;
    }
    
}