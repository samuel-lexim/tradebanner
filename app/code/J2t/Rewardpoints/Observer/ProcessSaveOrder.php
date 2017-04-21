<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessSaveOrder implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        
        if ($order->getCustomerId()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->get('J2t\Rewardpoints\Helper\Data');
            if (!$objectManager->get('J2t\Rewardpoints\Helper\Data')->getActive($order->getStoreId())){
                return $this;
            }
            $helper->recordPoints($order->getIncrementId(), $order->getCustomerId(), $order->getStoreId(), $order->getCreatedAt(), $order->getRewardpointsGathered(), $order->getRewardpointsQuantity(), true, $helper->getPointsDelay(), $helper->getPointsDuration(), $order->getState(), $order->getStatus());
            $helper->processReferralTreatment($order, $order->getQuote());
        }
    }

}
