<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Resource;

/**
 * AdminNotification Inbox model
 *
 * @author      J2T Design Team <contact@j2t-design.net>
 */
class Flatpoint extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * AdminNotification Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('rewardpoints_flat_account', 'flat_account_id');
    }
    
    public function loadByCustomerStore(\J2t\Rewardpoints\Model\Flatpoint $object, $customerId, $storeId, $date=null)
    {
        $adapter = $this->getConnection();
        //$adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable()
        )->where(
            'store_id = ?', $storeId
        )->where(
            'user_id = ?', $customerId
        );
        if ($date != null){
            $select->where("main_table.last_check = ?", $date);
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
    
    
    
    public function loadByReferralId(\J2t\Rewardpoints\Model\Point $object, $referral_id, $referral_customer_id = null)
    {
		$adapter = $this->getConnection();
        //$adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable()
        )->where(
            'rewardpoints_referral_id = ?', $referral_id
        );
        if ($referral_customer_id != NULL){
            $select->where(
                'customer_id != ?', $referral_customer_id
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
    
    
    public function loadByChildReferralId(\J2t\Rewardpoints\Model\Point $object, $referral_id, $referral_customer_id = null)
    {
		$adapter = $this->getConnection();
        //$adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable()
        )->where('rewardpoints_referral_id = ?',$referral_id);

        if ($referral_customer_id != null){
            $select->where('customer_id = ?', $referral_customer_id);
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
   
    public function loadByOrderIncrementId(\J2t\Rewardpoints\Model\Point $object, $order_id, $customer_id = null, $referral = false,  $parent = false)
    {
		$adapter = $this->getConnection();
        //$adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable()
        )->where('order_id = ?', $order_id);

        if ($parent && $customer_id){
            $select->where('customer_id != ?', $customer_id);
        } elseif ($customer_id) {
            $select->where('customer_id = ?', $customer_id);
        }
        if ($referral){
            $select->where('rewardpoints_referral_id IS NOT NULL');
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
    
    
    public function getPointsGathered(\J2t\Rewardpoints\Model\Point $object, $customer_id, $store_id)
    {
        $adapter = $this->getConnection();
        //$adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable(),
            [
                'points' => new \Zend_Db_Expr('SUM(points_current)')
            ]
        )->group(
            'customer_id'
        )->where(
            'customer_id=?',
            $customer_id
        )->where(
            'store_id=?',
            $store_id
        );
        //$return = $adapter->fetchPairs($select);
        //return $return;
        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }
        $this->_afterLoad($object);

        return $this;
    }
    
    public function getPointsSpent(\J2t\Rewardpoints\Model\Point $object, $customer_id, $store_id)
    {
		$adapter = $this->getConnection();
        //$adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable(),
            [
                'points' => new \Zend_Db_Expr('SUM(points_spent)')
            ]
        )->group(
            'customer_id'
        )->where(
            'customer_id=?',
            $customer_id
        )->where(
            'store_id=?',
            $store_id
        );
        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }
        $this->_afterLoad($object);

        return $this;
    }
    
    
}