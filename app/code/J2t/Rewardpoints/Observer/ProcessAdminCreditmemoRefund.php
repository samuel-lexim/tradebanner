<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessAdminCreditmemoRefund implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
		$creditMemo = $event->getCreditmemo();
		$order      = $creditMemo->getOrder();
		
		$pointsRefunded = $creditMemo->getRewardpointsGathered();
		$pointsRefundedUsed = $creditMemo->getRewardpointsUsed();
		
		if ($order->getIncrementId() && $order->getCustomerId() && ($pointsRefunded > 0 || $pointsRefundedUsed > 0)){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$model = $objectManager->create('J2t\Rewardpoints\Model\Point')->loadByIncrementId($order->getIncrementId(), $order->getCustomerId());
			$currentPoints = $model->getPointsCurrent();
			$usedPoints = $model->getPointsSpent();
			$model->setPointsCurrent($currentPoints - $pointsRefunded);
			$model->setPointsSpent($usedPoints - $pointsRefundedUsed);
			$model->save();	
		}
        return $this;
    }
}
