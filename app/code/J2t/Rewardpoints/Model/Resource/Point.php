<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model\Resource;

/**
 * AdminNotification Inbox model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Point extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * AdminNotification Resource initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('rewardpoints_account', 'rewardpoints_account_id');
    }

    public function loadByReferralId(\J2t\Rewardpoints\Model\Point $object, $referral_id, $referral_customer_id = null) {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
                $this->getMainTable()
            )->where(
                'rewardpoints_referral_id = ?', $referral_id
        );
        if ($referral_customer_id != NULL) {
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

    public function loadByChildReferralId(\J2t\Rewardpoints\Model\Point $object, $referral_id, $referral_customer_id = null) {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
                        $this->getMainTable()
                )->where('rewardpoints_referral_id = ?', $referral_id);

        if ($referral_customer_id != null) {
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

    public function loadByOrderIncrementId(\J2t\Rewardpoints\Model\Point $object, $order_id, $customer_id = null, $referral = false, $parent = false) {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
                        $this->getMainTable()
                )->where('order_id = ?', $order_id);

        if ($parent && $customer_id) {
            $select->where('customer_id != ?', $customer_id);
        } elseif ($customer_id) {
            $select->where('customer_id = ?', $customer_id);
        }
        if ($referral) {
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

    public function getPointsGathered(\J2t\Rewardpoints\Model\Point $object, $customer_id, $store_id) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $valid_statuses = $objectManager->get('J2t\Rewardpoints\Helper\Data')->getValidStatuses($store_id);
        $status_field_name = 'rewardpoints_' . $objectManager->get('J2t\Rewardpoints\Helper\Data')->getStatusField($store_id);
        $store_scope = $objectManager->get('J2t\Rewardpoints\Helper\Data')->isApplyStoreScope($store_id);

        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
                        $this->getMainTable(), [
                    'points' => new \Zend_Db_Expr('SUM(points_current)')
                        ]
                )->group(
                        'customer_id'
                )->where(
                'customer_id=?', $customer_id
                )/* ->where(
          $status_field_name.' in(?)',
          explode(',',$valid_statuses)
          ) */; //->orWhere('order_id in(?)', '-1');

        $valid_order_points = array_keys($object->getPointsDefaultTypeToArray());

        $conditions[] = $adapter->quoteInto(
                $status_field_name . ' in(?)', explode(',', $valid_statuses)
        );

        $conditions[] = $adapter->quoteInto(
                'order_id IN (?)', $valid_order_points
        );

        $select->where(sprintf('(%s)', implode(' OR ', $conditions)));

        if ($store_scope) {
            $select->where(
                    'store_id=?', $store_id
            );
        }
        //$return = $adapter->fetchPairs($select);
        //return $return;
        //echo $select->__toString();
        //die;

        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        } else {
            $object->setPoints(0);
        }
        $this->_afterLoad($object);

        return $this;
    }

    public function getPointsSpent(\J2t\Rewardpoints\Model\Point $object, $customer_id, $store_id) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $valid_used_statuses = $objectManager->get('J2t\Rewardpoints\Helper\Data')->getValidUsedStatuses($store_id);
        $status_field_name = 'rewardpoints_' . $objectManager->get('J2t\Rewardpoints\Helper\Data')->getStatusField($store_id);
        $store_scope = $objectManager->get('J2t\Rewardpoints\Helper\Data')->isApplyStoreScope($store_id);

        //$adapter = $this->_getReadAdapter();
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
                        $this->getMainTable(), [
                    'points' => new \Zend_Db_Expr('SUM(points_spent)')
                        ]
                )->group(
                        'customer_id'
                )->where(
                'customer_id=?', $customer_id
                )/* ->where(
          $status_field_name.' in(?)',
          explode(',',$valid_used_statuses)
          ) */;

        $valid_order_points = array_keys($object->getPointsDefaultTypeToArray());
        $conditions[] = $adapter->quoteInto(
                $status_field_name . ' in(?)', explode(',', $valid_used_statuses)
        );
        $conditions[] = $adapter->quoteInto(
                'order_id IN (?)', $valid_order_points
        );

        $select->where(sprintf('(%s)', implode(' OR ', $conditions)));

        if ($store_scope) {
            $select->where(
                    'store_id=?', $store_id
            );
        }

        //echo $select->__toString();
        //die;


        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        } else {
            $object->setPoints(0);
        }
        $this->_afterLoad($object);

        return $this;
    }

    public function getNotAvailableYetPoints(\J2t\Rewardpoints\Model\Point $object, $customer_id, $store_id) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $valid_statuses = $objectManager->get('J2t\Rewardpoints\Helper\Data')->getValidStatuses($store_id);
        $status_field_name = 'rewardpoints_' . $objectManager->get('J2t\Rewardpoints\Helper\Data')->getStatusField($store_id);
        $store_scope = $objectManager->get('J2t\Rewardpoints\Helper\Data')->isApplyStoreScope($store_id);

        //$adapter = $this->_getReadAdapter();
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
                        $this->getMainTable(), [
                    'points' => new \Zend_Db_Expr('SUM(points_current)')
                        ]
                )->group(
                        'customer_id'
                )->where(
                        'customer_id=?', $customer_id
                )->where(
                'date_start > NOW()'
                )/* ->where(
          $status_field_name.' in(?)',
          explode(',',$valid_statuses)
          ) */;

        $valid_order_points = array_keys($object->getPointsDefaultTypeToArray());
        $conditions[] = $adapter->quoteInto(
                $status_field_name . ' in(?)', explode(',', $valid_statuses)
        );
        $conditions[] = $adapter->quoteInto(
                'order_id IN (?)', $valid_order_points
        );

        $select->where(sprintf('(%s)', implode(' OR ', $conditions)));

        if ($store_scope) {
            $select->where(
                    'store_id=?', $store_id
            );
        }
        //$return = $adapter->fetchPairs($select);
        //return $return;
        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        } else {
            $object->setPoints(0);
        }
        $this->_afterLoad($object);

        return $this;
    }

    public function getPointsWaitingValidation(\J2t\Rewardpoints\Model\Point $object, $customer_id, $store_id) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $valid_statuses = $objectManager->get('J2t\Rewardpoints\Helper\Data')->getValidStatuses($store_id);
        $status_field_name = 'rewardpoints_' . $objectManager->get('J2t\Rewardpoints\Helper\Data')->getStatusField($store_id);
        $store_scope = $objectManager->get('J2t\Rewardpoints\Helper\Data')->isApplyStoreScope($store_id);

        //$adapter = $this->_getReadAdapter();
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
                        $this->getMainTable(), [
                    'points' => new \Zend_Db_Expr('SUM(points_current)')
                        ]
                )->group(
                        'customer_id'
                )->where(
                        'customer_id=?', $customer_id
                )->where(
                $status_field_name . ' not in(?)', explode(',', $valid_statuses)
        );

        if ($store_scope) {
            $select->where(
                    'store_id=?', $store_id
            );
        }

        //echo $select->__toString();
        //die;
        //$return = $adapter->fetchPairs($select);
        //return $return;
        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        } else {
            $object->setPoints(0);
        }
        $this->_afterLoad($object);

        return $this;
    }

}
