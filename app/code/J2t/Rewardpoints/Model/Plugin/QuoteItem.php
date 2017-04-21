<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Plugin;

use Closure;

class QuoteItem
{
    /**
     * Add bundle attributes to order data
     *
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);

		$fields = ["rewardpoints_gathered", "rewardpoints_gathered_float", "base_rewardpoints", "rewardpoints_used", "rewardpoints_catalog_rule_text"];
		foreach ($fields as $code){
			$orderItem->setData($code, $item->getData($code));
		}
		
        return $orderItem;
    }
}
