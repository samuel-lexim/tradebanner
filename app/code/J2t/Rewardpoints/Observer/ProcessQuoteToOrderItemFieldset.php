<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessQuoteToOrderItemFieldset implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
		$source = $event->getSource();
		$target = $event->getTarget();
		
		$fields = ["rewardpoints_gathered", "rewardpoints_gathered_float", "base_rewardpoints", "rewardpoints_used", "rewardpoints_catalog_rule_text"];
		$targetIsArray = is_array($target);
		$sourceIsArray = is_array($source);
		
		foreach ($fields as $code){
			if ($sourceIsArray) {
				$value = isset($source[$code]) ? $source[$code] : null;
			} elseif ($source instanceof \Magento\Framework\DataObject) {
				$value = $source->getData($code);
			}
			if ($targetIsArray) {
				$target[$code] = $value;
			} else {
				$target->setData($code, $value);
			}
		}
    }
}
