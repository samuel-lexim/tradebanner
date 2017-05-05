<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Cron;

/**
 * Captcha cron actions
 */
class RewardActions {

    const XML_PATH_NOTIFICATION_NOTIFICATION_DAYS = 'rewardpoints/notifications/notification_days';
    const XML_PATH_EXPIRY_NOTIFICATION_ACTIVE = 'rewardpoints/notifications/expiry_notification_active';
    const XML_PATH_NOTIFICATION_POINTS_NOTIFICATIONS = 'rewardpoints/status_notification/points_notifications';
    const XML_PATH_CRON_REMOVE = 'rewardpoints/points_validity/cron_remove';
    const XML_PATH_STATUS_FIELD = 'rewardpoints/points_validity/status_used';

    protected $_helper;
    protected $_adminHelper;
    protected $_mediaDirectory;
    protected $_storeManager;
    protected $_flatPointsFactory;
    protected $_scopeConfig;
    protected $_pointFactory;
    protected $_customerModel = null;
    protected $_gathered_points;
    protected $_pointData = null;
    protected $dateTime;

    public function __construct(
    \J2t\Rewardpoints\Model\FlatpointFactory $resLogFactory, \J2t\Rewardpoints\Model\PointFactory $pointFactory, \Magento\Framework\Stdlib\DateTime $dateTime, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Customer\Model\Customer $customer, \Magento\Captcha\Helper\Data $helper, \Magento\Captcha\Helper\Adminhtml\Data $adminHelper, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManager $storeManager
    ) {
        $this->_flatPointsFactory = $resLogFactory;
        $this->_helper = $helper;
        $this->_adminHelper = $adminHelper;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_pointFactory = $pointFactory;
        $this->_customerModel = $customer;
        $this->_pointData = $pointHelper;
        $this->dateTime = $dateTime;
    }
    
    public function execute()
    {
        $this->aggregateRewardpointsData();
        $this->recalculateEndingPoints();
        //$this->processCustomerNotifications();
    }

    protected function aggregateRewardpointsData() {
        //remove all points related to non-valid orders
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        foreach ($this->_storeManager->getStores() as $store) {
            if ($this->_scopeConfig->getValue(self::XML_PATH_CRON_REMOVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId())) {
                $status_field = $this->_scopeConfig->getValue(self::XML_PATH_STATUS_FIELD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
                
                $collection = $this->_pointFactory->create()->getCollection();
                
                //$collection = $objectManager->get('\J2t\Rewardpoints\Model\Point')->getCollection();
                $collection->getSelect()->where("main_table.rewardpoints_$status_field = ?", \Magento\Sales\Model\Order::STATE_CANCELED);
                $collection->getSelect()->where('find_in_set(?, main_table.store_id)', $store->getId());
                //$loaded_collection = $collection->load();

                if ($collection->count()) {
                    foreach ($collection as $reward_line) {
                        $reward_line->delete();
                    }
                }
            }
        }
        /*$this->recalculateEndingPoints();
        //1. Get all points per customer
        //1.1 Browse all store ids : $storeId
        $this->processCustomerNotifications();
        //$this->recalculateEndingPoints();*/
    }

    protected function recalculateEndingPoints() {
        $alreadyChecked = array();
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($this->_storeManager->getStores() as $store) {
            $storeId = $store->getId();
            $points = $this->_pointFactory->create()
            //$points = $objectManager->get('\J2t\Rewardpoints\Model\Point')
                    ->getResourceCollection()
                    ->addFinishFilter(0)
                    ->addValidPoints($storeId, true, true);
            //echo $points->getSelect()->__toString();
            //die;
            if ($points->getSize()) {
                foreach ($points as $currentPoint) {
                    $customerId = $currentPoint->getCustomerId();

                    if (!in_array($customerId, $alreadyChecked)) {
                        $alreadyChecked[] = $customerId;
                        //refresh points for this customer
                        foreach ($this->_storeManager->getStores() as $storeCustomer) {
                            //$model = $objectManager->get('\J2t\Rewardpoints\Model\Flatpoint');
                            //$model = $this->_flatPointsFactory;
                            $this->_flatPointsFactory->create()->processRecordFlat($customerId, $storeCustomer->getId(), false, true);
                        }
                    }
                }
            }
        }
    }
    
    protected function getGatheredPoints($customerId, $storeId) {
        if (!$this->_gathered_points) {
            $this->_gathered_points = $this->_pointData->getCustomerGatheredPoints($customerId, $storeId);
        }
        return $this->_gathered_points;
    }

    protected function processCustomerNotifications() {
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($this->_storeManager->getStores() as $store) {
            $storeId = $store->getId();
            $active = $this->_scopeConfig->getValue(self::XML_PATH_EXPIRY_NOTIFICATION_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());

            if ($active) {
                // POINT VALIDITY EXPIRATION VERIFICATION
                /* $duration = Mage::getStoreConfig(self::XML_PATH_POINTS_DURATION, $_eachStoreId);
                  if ($duration){ */
                //$days = Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_NOTIFICATION_DAYS, $storeId);
                $days = $this->_scopeConfig->getValue(self::XML_PATH_NOTIFICATION_NOTIFICATION_DAYS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
                
                $points = $this->_pointFactory->create()->getResourceCollection()
                //$points = $objectManager->get('\J2t\Rewardpoints\Model\Point')->getResourceCollection()
                        ->addFinishFilter($days)
                        ->addValidPoints($storeId);

                //echo $points->getSelect()->__toString();
                //die;

                if ($points->getSize()) {
                    foreach ($points as $currentPoint) {
                        $customerId = $currentPoint->getCustomerId();
                        
                        $customer = $this->_customerModel->load($customerId);
                        //$customer = $objectManager->get('\Magento\Customer\Model\Customer')->load($customerId);
                        if ($customer->getId() && ($customer->getStoreId() == $storeId || $customer->getStoreId() == "")) {
                            $points = $currentPoint->getNbCredit();
                            
                            //if ($this->_scopeConfig->getValue(self::XML_PATH_NOTIFICATION_NOTIFICATION_DAYS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()))

                            /* if (Mage::getStoreConfig('rewardpoints/default/flatstats', $storeId)){
                              $points_received = $objectManager->get('\J2t\Rewardpoints\Model\Flatpoint')->collectPointsCurrent($customerId, $storeId);
                              } else { */
                            //$points_received = $this->_flatPointsFactory->create()->getPointsCurrent($customerId, $storeId);
                            //$points_received = $this->getGatheredPoints($customerId, $storeId);
                            $flatModel = $this->_flatPointsFactory->create()->loadByCustomerStore($customerId, $storeId);
                            $points_received = $flatModel->getPointsCurrent();
                            
                            //$points_received = $objectManager->get('\J2t\Rewardpoints\Model\Point')->getPointsCurrent($customerId, $storeId);
                            //}
                            //2. check if total points >= points available
                            if ($points_received >= $points) {
                                //3. send notification email
                                //$customer = Mage::getModel('customer/customer')->load($customerId);
                                //$customer_store_id = ($customer->getStoreId()) ? $customer->getStoreId() : $storeId;
                                $this->_pointFactory->create()->sendNotification($customer, $store, $points, $days);
                                //$objectManager->get('\J2t\Rewardpoints\Model\Point')->sendNotification($customer, $store, $points, $days);
                            }
                        }
                    }
                }
                //}
                // CUSTOMER NOTIFICATIONS
                $this->customerPointsNotifications($store);
            }
        }
    }

    protected function customerPointsNotifications($store) {
        //$notifications = Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_POINTS_NOTIFICATIONS, $storeId);
        $notifications = $this->_scopeConfig->getValue(self::XML_PATH_NOTIFICATION_POINTS_NOTIFICATIONS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());

        $notifications_array = unserialize($notifications);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->get('\Magento\Email\Model\Template')->getResourceCollection();
        $arr_select = $collection->toOptionArray();

        /* $collection = Mage::getResourceModel('core/email_template_collection')
          ->load();
          $arr_select = $collection->toOptionArray(); */
        
        if (sizeof($notifications_array)) {
            foreach ($notifications_array as $notification) {
                if (isset($notification['min_value']) && isset($notification['max_value']) && isset($notification['duration'])) {
                    if ($notification['min_value'] < $notification['max_value'] && $notification['duration'] > 0) {
                        $template = $notification['template'];
                        $sender = $notification['sender'];
                        //\J2t\Rewardpoints\Model\FlatpointFactory $flatPointFactory,
                        //$points = $objectManager->get('\J2t\Rewardpoints\Model\Flatpoint')->getResourceCollection();
                        $points = $this->_getResourceModel()->getResourceCollection();
                        /* $points = Mage::getModel('rewardpoints/flatstats')
                          ->getResourceCollection(); */
                        $points->addStoreId($store->getId());
                        $points->addPointsRange((int) $notification['min_value'], (int) $notification['max_value']);
                        $points->addCheckNotificationDate((int) $notification['duration']);
                        //echo $points->getSelect()->__toString();
                        //die;
                        if ($points->getSize()) {
                            foreach ($points as $customerPoint) {
                                $customerId = $customerPoint->getUserId();
                                $pointsCurrent = $customerPoint->getData('points_current');
                                $customer = $this->_customerModel->load($customerId);
                                //$customer = Mage::getModel('customer/customer')->load($customerId);
                                if ($customer->getId() && ($customer->getStoreId() == $store->getId() || $customer->getStoreId() == "")) {
                                    //email template verification
                                    $emailTemplate = null;
                                    if ($template != "") {
                                        foreach ($arr_select as $trans_email) {
                                            if ($trans_email['value'] == $template) {
                                                $emailTemplate = $template;
                                            }
                                        }
                                    }
                                    $this->_getResourceModel()->sendCustomerNotification($customer, $store, $pointsCurrent, $customerPoint, $sender, $emailTemplate);
                                    //Mage::getModel('rewardpoints/flatstats')->sendCustomerNotification($customer, $storeId, $pointsCurrent, $customerPoint, $sender, $emailTemplate);
                                    //SET notification_date to today and increase notification_qty
                                    //$model = Mage::getModel('rewardpoints/flatstats')->load($customerPoint->getId());
                                    $model = $this->_getResourceModel()->load($customerPoint->getId());
                                    $model->setData('notification_qty', $model->getNotificationQty() + 1);
                                    $model->setData('notification_date', $this->dateTime->formatDate(time()));
                                    $model->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Delete Unnecessary logged attempts
     *
     * @return \Magento\Captcha\Model\Observer
     */
    public function deleteOldAttempts() {
        $this->_getResourceModel()->deleteOldAttempts();
        return $this;
    }

    /**
     * Get resource model
     *
     * @return \Magento\Captcha\Model\Resource\Log
     */
    protected function _getResourceModel() {
        return $this->_flatPointsFactory->create();
    }

}
