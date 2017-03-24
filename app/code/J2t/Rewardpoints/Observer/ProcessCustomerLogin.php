<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessCustomerLogin implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!$objectManager->get('J2t\Rewardpoints\Helper\Data')->getActive()){
            return $this;
        }
        
        $model = $objectManager->get('J2t\Rewardpoints\Model\Point')->loadByIncrementId(\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REGISTRATION, $customer->getId());
        $helper = $objectManager->get('J2t\Rewardpoints\Helper\Data');
        
        if (!$model->getId() && ($customerId = $customer->getId()) && ($points = $helper->getRegistrationPoints())) {
            $helper->recordPoints(\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REGISTRATION, $customerId, $customer->getStoreId(), date('Y-m-d'), $points, 0, true, $helper->getPointsDelay(), $helper->getPointsDuration(), null, null, false, true);
        }

        return $this;
    }

}
