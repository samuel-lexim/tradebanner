<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessSaveModel implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();
        //$customer = $event->getCustomer();
        $object = $observer->getEvent()->getObject();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('J2t\Rewardpoints\Helper\Data');
        if (!$helper->getActive()){
            return $this;
        }
        
        if ($object instanceof \Magento\Review\Model\Review) {
            if ($object->getStatusId() == \Magento\Review\Model\Review::STATUS_APPROVED) {
                if ($object->getId() && ($customerId = $object->getCustomerId()) && ($points = $helper->getPointsEventReview($object->getStoreId()))) {
                    $helper->recordPoints(\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REVIEW, $customerId, $object->getStoreId(), date('Y-m-d'), $points, 0, true, $helper->getPointsDelay(), $helper->getPointsDuration(), null, null, true);
                }
            }
        }
        if ($object instanceof \Magento\Newsletter\Model\Subscriber) {
            if ($object->getStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED) {
                if (($customerId = $object->getCustomerId()) && ($points = $helper->getPointsEventNewsletter($object->getStoreId()))) {
                    $helper->recordPoints(\J2t\Rewardpoints\Model\Point::TYPE_POINTS_NEWSLETTER, $customerId, $object->getStoreId(), date('Y-m-d'), $points, 0, true, $helper->getPointsDelay(), $helper->getPointsDuration(), null, null, false, true);
                }
                
                $customer = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create();
                $customer->setWebsiteId($object->getStoreId());
                $customer->loadByEmail($object->getSubscriberEmail());
                
                $ownerId = $customer->getId();
                if ($ownerId && ($points = $helper->getPointsEventNewsletter($object->getStoreId()))) {
                    $helper->recordPoints(\J2t\Rewardpoints\Model\Point::TYPE_POINTS_NEWSLETTER, $ownerId, $object->getStoreId(), date('Y-m-d'), $points, 0, true, $helper->getPointsDelay(), $helper->getPointsDuration(), null, null, false, true);
                }
            }
        }
        
        
    }

}
