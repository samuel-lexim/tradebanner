<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessAdminOrderCreate implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();
        $request = $observer->getRequestModel();
        $orderModel = $observer->getOrderCreateModel();

        $data = $request->getPost('order');
        $quote = $orderModel->getQuote();

        if (isset($data['rewardpoints']['qty']) && is_object($orderModel) && is_object($quote) && $quote->getId()) {
            if (is_numeric($data['rewardpoints']['qty'])) {
                $points = $data['rewardpoints']['qty'];
                $customerPoints = 0;
                if (($customerId = $quote->getCustomerId()) && ($storeId = $quote->getStoreId())) {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $helper = $objectManager->get('J2t\Rewardpoints\Helper\Data');
                    $customerPoints = $helper->getCurrentCustomerPoints($customerId, $storeId);
                }
                $points = ($customerPoints < $points) ? $customerPoints : $points;
                $quote->setRewardpointsQuantity($points)->setRecollect(true);
            }
        }
        return $this;
    }

}
