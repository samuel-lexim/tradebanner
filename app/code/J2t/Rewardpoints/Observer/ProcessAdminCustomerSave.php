<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessAdminCustomerSave implements ObserverInterface {

    protected $_objectManager;

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        $request = $event->getRequest();

        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($data = $request->getPost()) {

            $data_array = array(
                'points_current' => $data->points_current,
                'rewardpoints_description' => $data->rewardpoints_description,
                'date_start' => $data->date_start,
                'date_end' => $data->date_end,
                'rewardpoints_notification' => $data->rewardpoints_notification
            );

            if (isset($data['points_current'])) {
                if ($data['points_current'] > 0 || $data['points_current'] < 0) {

                    $model = $this->_objectManager->create('J2t\Rewardpoints\Model\Point');
                    $dateFilter = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\Filter\Date');
                    $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManager');

                    /*$inputFilter = new \Zend_Filter_Input(
                            ['date_start' => $dateFilter, 'date_end' => $dateFilter], [], $data_array
                    );*/
                    
                    $filterRules = [];
                    foreach (['date_start', 'date_end'] as $dateField) {
                        if (!empty($data_array[$dateField])) {
                            $filterRules[$dateField] = $dateFilter;
                        }
                    }

                    $data = (new \Zend_Filter_Input($filterRules, [], $data_array))->getUnescaped();
                    
                    
                    //$data = $inputFilter->getUnescaped();

                    if (($points = trim($data['points_current'])) && $points < 0) {
                        $data['points_spent'] = abs($data['points_current']);
                        unset($data['points_current']);
                    }

                    $customer_data = $this->_objectManager->get('\Magento\Customer\Model\Customer\Mapper')->toFlatArray($customer);
                    $storeId = (isset($customer_data['store_id'])) ? $customer_data['store_id'] : 0;

                    $ids = array();
                    if ($storeId) {
                        $data['store_id'] = $storeId;
                    } else {
                        foreach ($storeManager->getStores() as $store) {
                            $ids[] = $store->getId();
                        }
                        $data['store_id'] = implode(",", $ids);
                    }

                    $data['customer_id'] = $customer_data['id'];
                    $data['order_id'] = \J2t\Rewardpoints\Model\Point::TYPE_POINTS_ADMIN;

                    
                    $model->addData($data)->save();

                    $description = $data['rewardpoints_description'];
                    if ($description == "") {
                        $description = __('Store input');
                    }

                    if (!empty($data['rewardpoints_notification'])) { 
                        $helper = $this->_objectManager->get('J2t\Rewardpoints\Helper\Data');
                        $customerModel = $this->_objectManager->get('\Magento\Customer\Model\Customer')->load($customer->getId());
                        $helper->sendAdminNotification($model, $customerModel, $customer->getStoreId(), $data['points_current'], $description);
                        //$model->sendAdminNotification($customer, $customer->getStoreId(), $data['points_current'], $description);
                    }
                }
            }
        }
        return $this;
    }
}
