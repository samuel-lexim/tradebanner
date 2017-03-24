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
class Flatpoint extends \Magento\Framework\Model\AbstractModel {

    protected $points_current;
    protected $points_collected;
    protected $points_received;
    protected $points_spent;
    protected $points_waiting;
    protected $points_lost;
    protected $_targets, $_pointData;
    protected $_storeManager;
    protected $_customerModel = null;
    protected $_appEmulation;
    protected $inlineTranslation;
    protected $_scopeConfig;
    protected $_transportBuilder;
    protected $customerFactory;

    public function __construct(
    \Magento\Framework\Model\Context $context, 
            \Magento\Framework\Registry $registry, 
            \J2t\Rewardpoints\Helper\Data $pointHelper, 
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Magento\Customer\Model\Customer $customer = null, 
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, 
            //\Magento\Customer\Model\CustomerFactory $customerFactory,
            array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_pointData = $pointHelper;
        //$this->customerFactory = $customerFactory;
    }

    protected function _construct() {
        $this->_init('J2t\Rewardpoints\Model\Resource\Flatpoint');
    }

    public function sendCustomerNotification($customer, $store, $points, $pointModel, $sender, $emailTemplate = null) {
        return $this->_pointData->sendCustomerNotification($customer, $store, $points, $pointModel, $sender);
    }

    public function loadByCustomerStore($customerId, $storeId, $date = null) {
        $this->getResource()->loadByCustomerStore($this, $customerId, $storeId, $date);
        return $this;
    }

    public function loadByCustomerId($customer_id) {
        $collection = $this->getCollection();
        $collection->getSelect()->where('user_id = ?', $customer_id);

        return $collection;
    }

    protected function collectVariablesValues($customer_id, $storeId) {
        $this->loadByCustomerStore($customer_id, $storeId);
        $this->points_current = $this->getPointsCurrent();
        $this->points_collected = $this->getPointsCollected();
        $this->points_waiting = $this->getPointsWaiting();
        $this->points_spent = $this->getPointsUsed();
        $this->points_lost = $this->getPointsLost();
    }

    public function collectPointsCurrent($customer_id, $storeId) {
        if ($this->points_current != null) {
            return $this->points_current;
        }
        $this->collectVariablesValues($customer_id, $storeId);
        return $this->points_current;
    }

    public function collectPointsReceived($customer_id, $storeId) {
        if ($this->points_collected != null) {
            return $this->points_collected;
        }
        $this->collectVariablesValues($customer_id, $storeId);
        return $this->points_collected;
    }

    public function collectPointsSpent($customer_id, $storeId) {
        if ($this->points_spent != null) {
            return $this->points_spent;
        }
        $this->collectVariablesValues($customer_id, $storeId);
        return $this->points_spent;
    }

    public function collectPointsWaitingValidation($customer_id, $storeId) {
        if ($this->points_waiting != null) {
            return $this->points_waiting;
        }
        $this->collectVariablesValues($customer_id, $storeId);
        return $this->points_waiting;
    }

    public function collectPointsLost($customer_id, $storeId) {
        if ($this->points_lost != null) {
            return $this->points_lost;
        }
        $this->collectVariablesValues($customer_id, $storeId);
        return $this->points_lost;
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

    public function processRecordFlat($customerId, $storeId, $checkDate = false, $forceProcess = false) {
        if ($customerId) {
            $points_received = $this->_pointData->getCustomerGatheredPoints($customerId, $storeId);
            $points_spent = $this->_pointData->getCustomerSpentPoints($customerId, $storeId);
            $points_not_available = $this->_pointData->getCustomerNotAvailablePoints($customerId, $storeId);

            $points_awaiting_validation = $this->_pointData->getCustomerWaitingValidationPoints($customerId, $storeId);
            $points_lost = abs($this->_pointData->getCustomerExpiredPoints($customerId, $storeId));
            $points_current = $points_received - $points_spent - $points_lost;

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            //$this->_customerModel = $objectManager->get('\Magento\Customer\Model\Customer');
            $this->customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory');
            $this->_storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            
            
            $this->loadByCustomerStore($customerId, $storeId);
            if ((!$points_received && !$points_spent && !$points_awaiting_validation && !$points_current && !$points_lost) 
                    || ($points_received == $this->getPointsCollected() && $points_spent == $this->getPointsUsed() 
                            && $points_awaiting_validation == $this->getPointsWaiting() 
                            && $points_current == $this->getPointsCurrent() && $points_lost == $this->getPointsLost())) {
                if ($this->getId() && !$points_received && !$points_spent && !$points_awaiting_validation && !$points_current && !$points_lost) {
                    //remove line
                    $this->delete();
                    if ($websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId()) {
                        //$customer = $this->_customerModel->setWebsiteId($websiteId)->load($customerId);
                        $customer = $this->customerFactory->create()->setWebsiteId($websiteId)->load($customerId);
                        if ($customer->getId()) {
                            //$customer = $this->customerFactory->create()->load($customer->getId());
                            $customer->setRewardpointsAccumulated(0);
                            $customer->setRewardpointsAvailable(0);
                            $customer->setRewardpointsSpent(0);
                            $customer->setRewardpointsLost(0);
                            $customer->setRewardpointsWaiting(0);
                            $customer->setRewardpointsNotAvailable(0);
                            
                            $customerData = $customer->getDataModel();
                            $customerData->setCustomAttribute('rewardpoints_accumulated', 0);
                            $customerData->setCustomAttribute('rewardpoints_available', 0);
                            $customerData->setCustomAttribute('rewardpoints_spent', 0);
                            $customerData->setCustomAttribute('rewardpoints_lost', 0);
                            $customerData->setCustomAttribute('rewardpoints_waiting', 0);
                            $customerData->setCustomAttribute('rewardpoints_not_available', 0);
                            $customer->updateData($customerData);
                            
                            $customer->save();
                        }
                    }
                }
                return false;
            }

            $this->setPointsCollected($points_received);
            $this->setPointsUsed($points_spent);
            $this->setPointsWaiting($points_awaiting_validation);
            $this->setPointsCurrent($points_current);
            $this->setPointsLost($points_lost);
            $this->setPointsNotAvailable($points_not_available);
            $this->setStoreId($storeId);
            $this->setUserId($customerId);

            if ($checkDate && ($date_check = $this->getLastCheck())) {
                $date_array = explode("-", $this->getLastCheck());
                if ($this->getLastCheck() == date("Y-m-d")) {
                    return false;
                }
            }

            $this->setLastCheck(date("Y-m-d"));

            //var_dump($this->getData());
            //die;

            $this->save();

            if ($websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId()) {
                //$customer = $this->_customerModel->setWebsiteId($websiteId)->load($customerId);
                $customer = $this->customerFactory->create()->setWebsiteId($websiteId)->load($customerId);
                //var_dump($customer->getData());
                //$customer->getRewardpointsAccumulated();
                //die;
                if ($customer->getId()) {
                    //$customer = $this->customerFactory->create()->load($customer->getId());
                    $customer->setRewardpointsAccumulated($points_received);
                    $customer->setRewardpointsAvailable($points_current);
                    $customer->setRewardpointsSpent($points_spent);
                    $customer->setRewardpointsLost($points_lost);
                    $customer->setRewardpointsWaiting($points_awaiting_validation);
                    $customer->setRewardpointsNotAvailable($points_not_available);
                    
                    $customerData = $customer->getDataModel();
                    $customerData->setCustomAttribute('rewardpoints_accumulated', $points_received);
                    $customerData->setCustomAttribute('rewardpoints_available', $points_current);
                    $customerData->setCustomAttribute('rewardpoints_spent', $points_spent);
                    $customerData->setCustomAttribute('rewardpoints_lost', $points_lost);
                    $customerData->setCustomAttribute('rewardpoints_waiting', $points_awaiting_validation);
                    $customerData->setCustomAttribute('rewardpoints_not_available', $points_not_available);
                    $customer->updateData($customerData);
                    
                    
                    $customer->save();
                }
            }
        }
    }

}
