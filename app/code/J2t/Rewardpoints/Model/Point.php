<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model;

/**
 * Customer group model
 *
 * @method \Magento\Customer\Model\Resource\Group _getResource()
 * @method \Magento\Customer\Model\Resource\Group getResource()
 * @method string getCustomerGroupCode()
 * @method \Magento\Customer\Model\Group setCustomerGroupCode(string $value)
 * @method \Magento\Customer\Model\Group setTaxClassId(int $value)
 * @method Group setTaxClassName(string $value)
 */
class Point extends \Magento\Framework\Model\AbstractModel {

    const TARGET_PER_ORDER = 1;
    const TARGET_FREE = 2;
    const APPLY_ALL_ORDERS = '-1';
    const TYPE_POINTS_ADMIN = '-1';
    const TYPE_POINTS_REVIEW = '-2';
    const TYPE_POINTS_REGISTRATION = '-3';
    const TYPE_POINTS_REQUIRED = '-10';
    const TYPE_POINTS_BIRTHDAY = '-20';
    const TYPE_POINTS_FB = '-30';
    const TYPE_POINTS_GP = '-40';
    const TYPE_POINTS_PIN = '-50';
    const TYPE_POINTS_TT = '-60';
    const TYPE_POINTS_NEWSLETTER = '-70';
    const TYPE_POINTS_POLL = '-80';
    const TYPE_POINTS_TAG = '-90';
    const TYPE_POINTS_DYN = '-99';
    const TYPE_POINTS_REFERRAL_REGISTRATION = '-33';
    const XML_PATH_NOTIFICATION_EMAIL_TEMPLATE = 'rewardpoints/notifications/notification_email_template';
    const XML_PATH_NOTIFICATION_EMAIL_IDENTITY = 'rewardpoints/notifications/notification_email_identity';
    const XML_PATH_NOTIFICATION_ADMIN_EMAIL_TEMPLATE = 'rewardpoints/admin_notifications/notification_admin_email_template';
    const XML_PATH_NOTIFICATION_ADMIN_EMAIL_IDENTITY = 'rewardpoints/admin_notifications/notification_admin_email_identity';

    protected $_targets, $_pointData, $dateTime;
    protected $_flatPoint;
    protected $_storeManager = null;
    protected $_appEmulation;
    protected $inlineTranslation;
    protected $_scopeConfig;
    protected $_transportBuilder;

    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, \J2t\Rewardpoints\Model\Flatpoint $flatPoint = null, array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_pointData = $pointHelper;

    }

    protected function _construct() {
        $this->_init('J2t\Rewardpoints\Model\Resource\Point');

        $this->_targets = array(
            self::TARGET_PER_ORDER => __('Related to Order ID'),
            self::TARGET_FREE => __('Not related to Order ID'),
        );
    }

    public function sendNotification($customer, $store, $points, $days) {
        return $this->_pointData->sendNotification($customer, $store, $points, $days);
    }

    public function getOnlyPointsTypesArray() {
        $arr = array();
        foreach ($this->getPointsDefaultTypeToArray() as $key => $value) {
            $arr[] = $key;
        }
        return $arr;
    }

    public function getPointsDefaultTypeToArray() {
        $return_value = array(self::TYPE_POINTS_FB => __('Facebook Like points'), //OK
            self::TYPE_POINTS_PIN => __('Pinterest points'), //OK
            self::TYPE_POINTS_TT => __('Twitter points'), //OK
            self::TYPE_POINTS_GP => __('Google Plus points'), //OK
            self::TYPE_POINTS_BIRTHDAY => __('Birthday points'), //OK
            self::TYPE_POINTS_REQUIRED => __('Required points usage'), //OK
            self::TYPE_POINTS_REVIEW => __('Review points'), //OK
            self::TYPE_POINTS_DYN => __('Event points'), //OK
            self::TYPE_POINTS_NEWSLETTER => __('Newsletter points'), //OK
            self::TYPE_POINTS_POLL => __('Poll points'), //OK
            self::TYPE_POINTS_TAG => __('Tag points'), //OK
            self::TYPE_POINTS_ADMIN => __('Admin gift'), //OK
            self::TYPE_POINTS_REGISTRATION => __('Referral registration points'),
            self::TYPE_POINTS_REFERRAL_REGISTRATION => __('Registration points')); //OK

        if ($this->_pointData->getModuleManager()->isEnabled('J2t_Rewardshare')) {
            $return_value[\J2t\Rewardshare\Model\Point::TYPE_POINTS_SHARE] = __('Gift (shared points)');
        }
        return $return_value;
    }

    public function getPointsTypeToArray() {
        $return_value = array(self::TYPE_POINTS_FB => __('Facebook Like points'), //OK
            self::TYPE_POINTS_GP => __('Google Plus points'), //OK
            self::TYPE_POINTS_PIN => __('Pinterest points'), //OK
            self::TYPE_POINTS_TT => __('Twitter points'), //OK
            self::TYPE_POINTS_BIRTHDAY => __('Birthday points'), //OK
            self::TYPE_POINTS_REVIEW => __('Review points'), //OK
            self::TYPE_POINTS_DYN => __('Event points'), //OK
            self::TYPE_POINTS_NEWSLETTER => __('Newsletter points'), //OK
            self::TYPE_POINTS_POLL => __('Poll points'), //OK
            self::TYPE_POINTS_TAG => __('Tag points'), //OK
            self::TYPE_POINTS_ADMIN => __('Admin gift'), //OK
            self::TYPE_POINTS_REQUIRED => __('Points used on products'),
            self::TYPE_POINTS_REGISTRATION => __('Referral registration points'),
            self::TYPE_POINTS_REFERRAL_REGISTRATION => __('Registration points')); //OK

        if ($this->_pointData->getModuleManager()->isEnabled('J2t_Rewardshare')) {
            $return_value[\J2t\Rewardshare\Model\Point::TYPE_POINTS_SHARE] = __('Gift (shared points)');
        }
        return $return_value;
    }

    public function getTargetsArray() {
        return $this->_targets;
    }

    public function targetsToOptionArray() {
        return $this->_toOptionArray($this->_targets);
    }

    protected function _toOptionArray($array) {
        $res = array();
        foreach ($array as $value => $label) {
            $res[] = array('value' => $value, 'label' => $label);
        }
        return $res;
    }

    public function loadByIncrementId($incrementId, $customerId) {
        //return $this->_getResource()->loadByOrderIncrementId($this, $incrementId, $customerId);
        $this->getResource()->loadByOrderIncrementId($this, $incrementId, $customerId);
        return $this;
    }

    public function loadGatheredPoints($customerId, $storeId) {
        $this->getResource()->getPointsGathered($this, $customerId, $storeId);
        return $this;
    }

    public function loadSpentPoints($customerId, $storeId) {
        $this->getResource()->getPointsSpent($this, $customerId, $storeId);
        return $this;
    }

    public function loadNotAvailableYetPoints($customerId, $storeId) {
        $this->getResource()->getNotAvailableYetPoints($this, $customerId, $storeId);
        return $this;
    }

    public function loadPointsWaitingValidation($customerId, $storeId) {
        $this->getResource()->getPointsWaitingValidation($this, $customerId, $storeId);
        return $this;
    }

    public function constructSqlPointsType($tablePrefix, $specificTypes = array()) {
        $arr_sql = array();
        foreach ($this->getPointsDefaultTypeToArray() as $key => $value) {
            if ($specificTypes == array()) {
                $arr_sql[] = $tablePrefix . ".order_id = '" . $key . "' ";
            } elseif (in_array($key, $specificTypes)) {
                $arr_sql[] = $tablePrefix . ".order_id = '" . $key . "' ";
            }
        }
        return implode(" or ", $arr_sql);
    }

    /**
     * 
     * @param type $used_point: all points that have been used
     * @param type $list_points: list of all gathered points ordered by date
     * @param type $left_over: quantity that is left over
     * @param type $left_over_datestamp: date of quantity that is left over
     */
    protected function calculateLostPointsLeft($used_points, $list_points) {
        $expired_unused_points = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$object = $objectManager->get('\Magento\Framework\Stdlib\DateTime');
        //$today_stamp = $object->toTimestamp($object->now());
        $today_stamp = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        
        if (!is_numeric($today_stamp)){
            $today_stamp = (new \Zend_Date($today_stamp, \Zend_Date::ISO_8601))->getTimestamp();
        }
        
        //$today_stamp = time();
        
        /*echo $today_stamp;
        echo ' '.time();
        die;*/

        $original_list_points = $list_points;
        //1. list all used points
        //2. verify if all gathered points have been used for used points
        $len = count($list_points);
        $len2 = count($used_points);
        if ($len && $len2) {
            $valid_points = $this->getPointsDefaultTypeToArray();
            foreach ($used_points as &$used_point) {
                $i = 0;
                $end = false;
                $date_used_iso = new \Zend_Date($used_point['date_used'], \Zend_Date::ISO_8601);
                $date_used_stamp = $date_used_iso->getTimestamp();
                //while ($used_point['value'] > 0 && !$end){
                while (!$end) {
                    foreach ($list_points as &$point) {
                        //verify point usage date against point gathering 
                        //and check if order is different / point gathered through the same order cannot be used
                        $date_form_iso = new \Zend_Date($point['date_from'], \Zend_Date::ISO_8601);
                        $from_datestamp = $date_form_iso->getTimestamp();
                        
                        $date_to_iso = new \Zend_Date($point['date_end'], \Zend_Date::ISO_8601);
                        $end_datestamp = $date_to_iso->getTimestamp();

                        if ($point['points'] > 0 && $date_used_stamp >= $from_datestamp && $date_used_stamp <= $end_datestamp && ( ( $point['order_id'] != null && isset($valid_points[$point['order_id']]) ) || ($used_point['order_id'] != $point['order_id'] || $point['order_id'] == null) )) {
                            $points_left = $point['points'] - $used_point['value'];
                            //if user has used more points that he has collected > set 0 in point gathered value for this date and modify used points value according to what's left
                            if ($points_left < 0) {
                                $used_point['value'] = $used_point['value'] - $point['points'];
                                //echo "New used point value {$used_point['value']} ({$used_point['value']} - {$point['points']}) <br />";
                                $point['points'] = 0;
                            }
                            //if still have point's left (or 0), modify used point value
                            else {
                                $point['points'] = $points_left;
                                $used_point['value'] = 0;
                                $end = true;
                                break;
                            }
                        }
                        //check if while loop isn't going into enless loop
                        if ($i == $len - 1) {
                            //last element - end while
                            $end = true;
                        }
                        $i++;
                    }
                }
            }

            //sum of all expired point left prior today's date
            foreach ($list_points as $point_current) {
                $date_to_iso = new \Zend_Date($point_current['date_end'], \Zend_Date::ISO_8601);
                $end_datestamp = $date_to_iso->getTimestamp();

                $date_from_iso = new \Zend_Date($point_current['date_from'], \Zend_Date::ISO_8601);
                $from_datestamp = $date_from_iso->getTimestamp();
                if ($today_stamp < $from_datestamp || $today_stamp >= $end_datestamp) {
                    $expired_unused_points += $point_current['points'];
                }
            }
        } else if ($len) {
            //sum of all points that are not valid yet or expired
            foreach ($original_list_points as $point_current) {
                $date_from_iso = new \Zend_Date($point_current['date_from'], \Zend_Date::ISO_8601);
                $from_datestamp = $date_from_iso->getTimestamp();
                $date_to_iso = new \Zend_Date($point_current['date_end'], \Zend_Date::ISO_8601);
                $end_datestamp = $date_to_iso->getTimestamp();
                if ($today_stamp < $from_datestamp || $today_stamp >= $end_datestamp) {
                    $expired_unused_points += $point_current['points'];
                }
            }
        }
        return -$expired_unused_points;
    }

    public function getPointsReceivedReajustment($customerId, $storeId) {
        $points = $this
                ->getResourceCollection()
                ->addUsedpointsbydate($storeId, $customerId);

        
        $valid_points = $this->getResourceCollection()->loadallpointsbydate($storeId, $customerId);
        
        $arr_points_collection = array();

        /*
         * $arr_points_collection : all gathered points having begining and ending date
         */
        if ($valid_points->count()) {
            foreach ($valid_points as $valid_point) {
                $arr_points_collection[] = array("points" => $valid_point->getData('points_current'),
                    "points_calculated" => $valid_point->getData('points_current'),
                    "points_spent" => $valid_point->getData('points_spent'),
                    "order_id" => $valid_point->getData('order_id'),
                    "date_from" => ($valid_point->getData('date_start')) ? $valid_point->getData('date_start') : date('Y-m-d', mktime(0, 0, 0, 1, 1, 1970)),
                    "date_end" => ($valid_point->getData('date_end')) ? $valid_point->getData('date_end') : date('Y-m-d', mktime(0, 0, 0, 1, 1, date("Y") + 1))
                );
            }
        }
        
        /*
         * $points : all used points groupped by used dates (date_insertion used instead of date_order, 
         * in order to avoid any issues related to missing dates - e.g. missing date_order when inserting points through admin)
         */
        $used_points = array();

        if ($points->getSize() && sizeof($arr_points_collection)) {
            foreach ($points as $used_point) {
                $date_start = ($used_point->getData('date_start')) ? $used_point->getData('date_start') : date("Y-m-d");
                $used_points[] = array(
                    "points_used" => $used_point->getData('points_spent'),
                    "value" => $used_point->getData('points_spent'),
                    "order_id" => $used_point->getData('order_id'),
                    "date_used" => ($used_point->getData('date_order')) ? $used_point->getData('date_order') : $date_start
                );
            }
        }
       
        return $this->calculateLostPointsLeft($used_points, $arr_points_collection);
    }

    /* public function loadByIncrementId($incrementId)
      {
      $ids = $this->getCollection()->addAttributeToFilter('order_id', $incrementId)->getAllIds();

      if (!empty($ids)) {
      reset($ids);
      $this->load(current($ids));
      }
      return $this;
      } */

    protected function updateFlatRecords() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_flatPoint = $objectManager->get('\J2t\Rewardpoints\Model\Flatpoint');
        $this->_storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        if ($customerId = $this->getCustomerId()) {
            $storeIds = explode(',', $this->getStoreId());
            $process_points = true;
            foreach ($storeIds as $storeId) {
                if ($storeId) {
                    $this->_flatPoint->processRecordFlat($customerId, $storeId, false, true);
                    $process_points = false;
                }
            }
            if ($process_points) {
                foreach ($this->_storeManager->getStores() as $store) {
                    $this->_flatPoint->processRecordFlat($customerId, $store->getId(), false, true);
                }
            }
        }
    }

    public function beforeSave() {
        //$this->updateFlatRecords();
        if (!$this->hasDateInsertion() || !$this->getDateInsertion()){
            $this->setDateInsertion(date("Y-m-d"));
        }
        if (!$this->hasPeriod() || !$this->getPeriod()){
            $this->setPeriod(date("Y-m-d"));
        }
        return parent::beforeSave();
    }
    
    public function afterSave() {
        $this->updateFlatRecords();
        return parent::afterSave();
    }

    public function afterDelete() {
        $this->updateFlatRecords();
        return parent::afterDelete();
    }

}
