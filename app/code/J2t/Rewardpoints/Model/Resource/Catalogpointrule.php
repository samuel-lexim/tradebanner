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
class Catalogpointrule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * AdminNotification Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('rewardpoints_catalogrules', 'rule_id');
    }
    
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $fromDate = $object->getFromDate();
        if ($fromDate instanceof \Zend_Date) {
            $object->setFromDate($fromDate->toString(\Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT));
        } elseif (!is_string($fromDate) || empty($fromDate)) {
            $object->setFromDate(null);
        }

        $toDate = $object->getToDate();
        if ($toDate instanceof \Zend_Date) {
            $object->setToDate($toDate->toString(\Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT));
        } elseif (!is_string($toDate) || empty($toDate)) {
            $object->setToDate(null);
        }

        parent::_beforeSave($object);
        return $this;
        
    }
    
}