<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessQuoteSave implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
		$order = $event->getOrder();
        $quote = $event->getQuote();
        
        $order->setRewardpointsDescription($quote->getRewardpointsDescription());
        $order->setRewardpointsQuantity($quote->getRewardpointsQuantity());
        $order->setBaseRewardpoints($quote->getBaseRewardpoints());
        $order->setRewardpoints($quote->getRewardpoints());
        $order->setRewardpointsReferrer($quote->getRewardpointsReferrer());
        $order->setRewardpointsGathered($quote->getRewardpointsGathered());
        $order->setRewardpointsCartRuleText($quote->getRewardpointsCartRuleText());
        
        return $this;
    }
}
