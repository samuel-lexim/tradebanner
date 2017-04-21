<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessSaveCustomer implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();
        //$customer = $event->getCustomer();
        $customer = $event->getCustomer();
        
        /*var_dump($customer->getData());
        die;*/
        
        if ($customer->getId()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->get('J2t\Rewardpoints\Helper\Data');
            $rewardSession = $objectManager->get('J2t\Rewardpoints\Model\Session');
            $referralFactory = $objectManager->get('J2t\Rewardpoints\Model\ReferralFactory');
            
            if (!$helper->getActive()){
                return $this;
            }
            
            if ($rewardSession->getReferralUser() == $customer->getId()) {
                $rewardSession->setReferralUser(null);
                $customer->setRewardpointsReferrer(null);
            }
            if ($rewardSession->getReferralUser()) {
                $userId = $rewardSession->getReferralUser();
                if (!$customer->getRewardpointsReferrer() && ($email = $customer->getEmail())) {
                    $customer->setRewardpointsReferrer($userId);
                    $referralModel = $referralFactory->create();
                    
                    $verifyStoreId = null;
                    if ($helper->isApplyStoreScope($customer->getStoreId())){
                        $verifyStoreId = $customer->getStoreId();
                    }
                    
                    if (!$referralModel->isSubscribed($email, $verifyStoreId)) {
                        $referralModel->setRewardpointsReferralParentId($userId)
                                ->setRewardpointsReferralEmail($email)
                                ->setRewardpointsReferralName($customer->getName())
                                ->setStoreId($customer->getStoreId());
                        $referralModel->save();
                    }
                    
                    
                }
            }
            
            //var_dump($customer->getData());
            //die;
        }
    }

}
