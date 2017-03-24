<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Model\Quote\Address\ToOrder as QuoteAddressToOrder;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

class PointOrder 
{
    /*public function afterGetTitle($subject, $proceed){
        return $proceed." << ";
    }*/
    protected $quoteAddress;
    
    public function beforeConvert(QuoteAddressToOrder $subject, QuoteAddress $address, $additional = [])
    {
        $this->quoteAddress = $address;
        return [$address, $additional];
    }
    
    public function afterConvert(QuoteAddressToOrder $subject, OrderInterface $order)
    {
        $order->setConvertingFromQuote(true);
        //$order->setRewardpointsDescription($this->quoteAddress->getRewardpointsDescription());
        $order->setRewardpointsDescription("test");
        $order->setRewardpointsQuantity($this->quoteAddress->getRewardpointsQuantity());
        $order->setBaseRewardpoints($this->quoteAddress->getBaseRewardpoints());
        $order->setRewardpoints($this->quoteAddress->getRewardpoints());
        $order->setRewardpointsReferrer($this->quoteAddress->getRewardpointsReferrer());
        $order->setRewardpointsGathered($this->quoteAddress->getRewardpointsGathered());
        $order->setRewardpointsCartRuleText($this->quoteAddress->getRewardpointsCartRuleText());
        
        return $order;
    }
    
    
    /*public function aroundSubmitQuote(
        Magento\Sales\Model\Order $subject,
        \Closure $proceed,
        $object,
        $orderData = []
    ) {*/
    public function aroundSubmitQuote(
        $subject,
        $proceed,
        $quote,
        $orderData = []
    ) {
        //$subject->orderBuilder->setRewardpointsDescription($quote->getRewardpointsDescription());
        $subject->orderBuilder->setRewardpointsDescription("test");
        $subject->orderBuilder->setRewardpointsQuantity($quote->getRewardpointsQuantity());
        $subject->orderBuilder->setBaseRewardpoints($quote->getBaseRewardpoints());
        $subject->orderBuilder->setRewardpoints($quote->getRewardpoints());
        $subject->orderBuilder->setRewardpointsReferrer($quote->getRewardpointsReferrer());
        $subject->orderBuilder->setRewardpointsGathered($quote->getRewardpointsGathered());
        $subject->orderBuilder->setRewardpointsCartRuleText($quote->getRewardpointsCartRuleText());
        
        
        /*$order = $proceed;
        //$order->setRewardpointsDescription($order->getQuote()->getRewardpointsDescription());
        $order->setRewardpointsDescription("test");
        $order->setRewardpointsQuantity($order->getQuote()->getRewardpointsQuantity());
        $order->setBaseRewardpoints($order->getQuote()->getBaseRewardpoints());
        $order->setRewardpoints($order->getQuote()->getRewardpoints());
        $order->setRewardpointsReferrer($order->getQuote()->getRewardpointsReferrer());
        $order->setRewardpointsGathered($order->getQuote()->getRewardpointsGathered());
        $order->setRewardpointsCartRuleText($order->getQuote()->getRewardpointsCartRuleText());*/
        
        return $proceed($object, $orderData);
    }
    
    /*
public function create()
    {
        $order = $this->orderFactory->create([
            'data' => [
                'quote_id' => $this->quoteId,
                'applied_rule_ids' => $this->appliedRuleIds,
                'is_virtual' => $this->isVirtual,
                'remote_ip' => $this->remoteIp,
                'base_subtotal' => $this->baseSubtotal,
                'subtotal' => $this->subtotal,
                'base_grand_total' => $this->baseGrandTotal,
                'grand_total' => $this->grandTotal,
                'base_currency_code' => $this->baseCurrencyCode,
                'global_currency_code' => $this->globalCurrencyCode,
                'store_currency_code' => $this->storeCurrencyCode,
                'store_id' => $this->storeId,
                'store_to_base_rate' => $this->storeToBaseRate,
                'base_to_global_rate' => $this->baseToGlobalRate,
                'coupon_code' => $this->couponCode,
                'customer_dob' => $this->customer->getDob(),
                'customer_email' => $this->customer->getEmail(),
                'customer_firstname' => $this->customer->getFirstName(),
                'customer_gender' => $this->customer->getGender(),
                'customer_group_id' => $this->customer->getGroupId(),
                'customer_id' => $this->customer->getId(),
                'customer_is_guest' => $this->customer->getIsGuest(),
                'customer_lastname' => $this->customer->getLastName(),
                'customer_middlename' => $this->customer->getMiddleName(),
                'customer_note' => $this->customer->getNote(),
                'customer_note_notify' => $this->customer->getNoteNotify(),
                'customer_prefix' => $this->customer->getPrefix(),
                'customer_suffix' => $this->customer->getSuffix(),
                'customer_taxvat' => $this->customer->getTaxvat(),
            ],
        ]);
        $order->setBillingAddress($this->billingAddress)
            ->setShippingAddress($this->shippingAddress);
        foreach ($this->items as $item) {
            if ($item instanceof Item) {
                $order->addItem($item);
            } else {
                throw new \InvalidArgumentException('Cannot add item, instance of wrong type is given');
            }
        }
        foreach ($this->payments as $payment) {
            if ($payment instanceof Payment) {
                $order->addPayment($payment);
            } else {
                throw new \InvalidArgumentException('Cannot add payment, instance of wrong type is given');
            }
        }
        return $order;
    }
     * 
     *      */
}
