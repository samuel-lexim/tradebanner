<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model\Creditmemo;

class Point extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal {

    protected $_pointsHelper;

    public function __construct(\J2t\Rewardpoints\Helper\Data $pointHelper, array $data = []) {
        $this->_pointsHelper = $pointHelper;
        parent::__construct($data);
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {
        //TODO: recalculate real gathering point value according to refunded products. Check Magento\Sales\Model\Order\Creditmemo\Total\Discount for example
        //math is done according to items lines and points gathered per lines (remove quantity according to discount value)
        $baseRefundTotalCredit = 0;
        $baseRefundTotalCreditUsed = 0;
        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            /* if ($orderItem->isDummy() || $item->getQty() <= 0) {

              $orderItemQty = (double)$orderItem->getQtyInvoiced();
              $rewardpointsGathered = $orderItem->getRewardpointsGathered();
              $availableQty = $orderItemQty - $orderItem->getQtyRefunded();
              if ($rewardpointsGathered && $orderItem->getQtyRefunded()){
              $unitPoints  = $rewardpointsGathered / $orderItemQty;
              $baseRefundTotalCredit += $unitPoints * $orderItem->getQtyRefunded();
              } */
            //$item contains all ordered items (minus refunded ones)

            if ($orderItem->isDummy()) {
                continue;
            }

            $refundQty = $item->getQty();
            //$previousQty = $orderItem->getQtyOrdered() - $orderItem->getQtyRefunded();
            $orderedQty = $orderItem->getQtyOrdered();
            $rewardpointsGathered = $orderItem->getRewardpointsGathered();
            $rewardpointsUsed = $orderItem->getRewardpointsUsed();

            if ($orderedQty > $refundQty) {
                if ($rewardpointsGathered) {
                    $unitPoint = $rewardpointsGathered / $orderedQty;
                    $baseRefundTotalCredit += $unitPoint * ($orderedQty - $refundQty);
                    $item->setRewardpointsGathered($unitPoint * $refundQty);
                }
                if ($rewardpointsUsed) {
                    $unitPointUsed = $rewardpointsUsed / $orderedQty;
                    $baseRefundTotalCreditUsed += $unitPointUsed * ($orderedQty - $refundQty);
                    $item->setRewardpointsUsed($unitPointUsed * $refundQty);
                }
            }

            //var_dump($item->getData());
            //var_dump($orderItem->getData());

            /* if (!$item->getHasChildren()) {
              $baseRefundTotalCredit += $item->getBaseCost() * $item->getQty();
              } */
        }

        $order = $creditmemo->getOrder();
        $baseRefundTotalCredit = $this->_pointsHelper->mathActionOnTotalCreditMemoPoints($baseRefundTotalCredit, $creditmemo->getOrder()->getStoreId());
        $creditmemo->setRewardpointsGathered($order->getRewardpointsGathered() - $baseRefundTotalCredit);
        $baseRefundTotalCreditUsed = $this->_pointsHelper->mathActionOnTotalCreditMemoPoints($baseRefundTotalCreditUsed, $creditmemo->getOrder()->getStoreId());
        $creditmemo->setRewardpointsUsed($order->getRewardpointsUsed() - $baseRefundTotalCreditUsed);
        return $this;
    }

}
