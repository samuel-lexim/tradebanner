<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessQuoteToOrder implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
		$event->getOrder()->setRewardpointsDescription($event->getQuote()->getRewardpointsDescription());
        $event->getOrder()->setRewardpointsQuantity($event->getQuote()->getRewardpointsQuantity());
        $event->getOrder()->setBaseRewardpoints($event->getQuote()->getBaseRewardpoints());
        $event->getOrder()->setRewardpoints($event->getQuote()->getRewardpoints());
        $event->getOrder()->setRewardpointsReferrer($event->getQuote()->getRewardpointsReferrer());
        $event->getOrder()->setRewardpointsGathered($event->getQuote()->getRewardpointsGathered());
        $event->getOrder()->setRewardpointsCartRuleText($event->getQuote()->getRewardpointsCartRuleText());
        //TODO - verify it save is still necessary after beta to release candidate
        //$event->getOrder()->save();
        return $this;
    }
}
