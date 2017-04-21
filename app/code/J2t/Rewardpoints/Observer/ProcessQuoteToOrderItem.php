<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessQuoteToOrderItem implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
		$quoteItem = $event->getItem();
		$orderItem = $event->getOrderItem();
		$fields = ["rewardpoints_gathered", "rewardpoints_gathered_float", "base_rewardpoints", "rewardpoints_used", "rewardpoints_catalog_rule_text"];
		foreach ($fields as $code){
			$orderItem->setData($code, $quoteItem->getData($code));
		}
		$observer->getEvent()->getOrderItem()->setRewardpointsGathered($observer->getEvent()->getItem()->getRewardpointsGathered());
		$observer->getEvent()->getOrderItem()->setRewardpointsGatheredFloat($observer->getEvent()->getItem()->getRewardpointsGatheredFloat());
		return $this;
    }
}
