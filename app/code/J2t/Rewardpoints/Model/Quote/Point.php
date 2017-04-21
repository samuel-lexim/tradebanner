<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class Point extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

    /**
     * Discount calculation object
     *
     * @var \Magento\SalesRule\Model\Validator
     */
    protected $_calculator;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Framework\Store\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    protected $_pointHelper = null;
    protected $_customerPoints = null;
    private $rewardSession;
    
    protected $messageManager;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Store\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
    \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Store\Model\StoreManagerInterface $storeManager, \J2t\Rewardpoints\Model\Validator $validator, PriceCurrencyInterface $priceCurrency, \J2t\Rewardpoints\Helper\Data $salesPointHelper, MessageManagerInterface $messageManager, \J2t\Rewardpoints\Model\Session $rewardSession
    ) {
        $this->_eventManager = $eventManager;
        $this->_pointHelper = $salesPointHelper;
        $this->messageManager = $messageManager;
        $this->setCode('rewardpoints');
        $this->_calculator = $validator;
        $this->_storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->rewardSession = $rewardSession;
    }

    protected function getCustomerPoints($address) {
        if ($this->_customerPoints === null) {
            $this->_customerPoints = $this->_pointHelper->getCurrentCustomerPoints($address->getQuote()->getCustomerId(), $address->getQuote()->getStoreId());
        }
        return $this->_customerPoints;
    }

    /**
     * Collect address discount amount
     *
     * @param Address $address
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function collect(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment, \Magento\Quote\Model\Quote\Address\Total $total) {
        $address = $shippingAssignment->getShipping()->getAddress();
        $rewardHelper = $this->_pointHelper;

        /*if ($quote->getIsMultiShipping() && $quote->getRewardpointsQuantity() > 0){
            $this->messageManager->addError(
                __('Points cannot be redeemed within multiple shipping orders.')
            );
            $quote->setRewardpointsQuantity(0);
        }*/
        if (!$rewardHelper->getActive($quote->getStoreId()) || $quote->getByPassRewards()) {
            return parent::collect($quote, $shippingAssignment, $total);
        }
        parent::collect($quote, $shippingAssignment, $total);
        $store = $this->_storeManager->getStore($quote->getStoreId());

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $quote->setBaseRewardpoints(0);
        $quote->setRewardpoints(0);

        $eventArgs = [
            'website_id' => $store->getWebsiteId(),
            'customer_group_id' => $quote->getCustomerGroupId(),
            'rewardpoints_quantity' => $quote->getRewardpointsQuantity(),
        ];

        $currentCustomerPoints = $this->getCustomerPoints($address);

        if (!$this->_pointHelper->checkCustomerMinPoints($currentCustomerPoints, $quote->getStoreId())) {
            $quote->setRewardpointsQuantity(0);
        }

        $pointsUsed = $quote->getRewardpointsQuantity();
        
        $maxPointUsage = $this->getMaxPointUsage($address, $pointsUsed);
        if ($pointsUsed != $maxPointUsage) {
            $pointsUsed = $maxPointUsage;
            $quote->setRewardpointsQuantity($pointsUsed);
        }
        
        if ($quote->getRewardpointsQuantity() > 0) {
            $points_value = $rewardHelper->getPointMoneyEquivalence($quote->getRewardpointsQuantity(), true, $quote, $quote->getStoreId());
            $quote->setBaseRewardpoints($points_value);
            $quote->setRewardpoints($this->priceCurrency->convert($points_value));
        }

        //var_dump($this->rewardSession->getReferralUser());
        if ($this->rewardSession->getReferralUser() == $quote->getCustomerId()) {
            $this->rewardSession->setReferralUser(null);
            $quote->setRewardpointsReferrer(null);
        }

        if ($userId = $this->rewardSession->getReferralUser()) {
            $quote->setRewardpointsReferrer($userId);
        }
        
        $realPointUsage = 0;
        $originalPointsUsed = $pointsUsed;

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {

            // to determine the child item discount, we calculate the parent
            if ($item->getParentItem()) {
                continue;
            }

            $eventArgs['item'] = $item;
            $this->_eventManager->dispatch('rewardpoints_quote_address_discount_item', $eventArgs);

            $inclTax = $this->_pointHelper->getIncludeTax($quote->getStoreId());
            $baseDiscountAmount = abs($item->getBaseDiscountAmount());
            
            if ($inclTax){
                $itemPrice = $item->getBaseRowTotalInclTax() - $baseDiscountAmount;
            } else {
                $itemPrice = $item->getBaseRowTotal() - $baseDiscountAmount;
            }
            
            $itemPriceInPoints = $this->_pointHelper->getPointsProductPriceEquivalence($itemPrice, $quote->getStoreId());
            $pointsToBeApplied = min($pointsUsed, $itemPriceInPoints);
            $item->setRewardpointsUsed($pointsToBeApplied);
            $realPointUsage += $pointsToBeApplied;
            
            /*$rewardDiscountAmount = $this->_pointHelper->getPointMoneyEquivalence($pointsToBeApplied, true, $quote, $quote->getStoreId());
            $baseRewardDiscountAmount = $this->priceCurrency->convert($rewardDiscountAmount);
            
            $basePointDiscountAmount = $baseDiscountAmount + $this->_pointHelper->getPointMoneyEquivalence($pointsToBeApplied, true, $quote, $quote->getStoreId());
            $discountAmount = $this->priceCurrency->convert($basePointDiscountAmount);*/
            
            
            $baseRewardDiscountAmount = $this->_pointHelper->getPointMoneyEquivalence($pointsToBeApplied, true, $quote, $quote->getStoreId());
            $rewardDiscountAmount = $this->priceCurrency->convert($baseRewardDiscountAmount);
            
            $basePointDiscountAmount = $baseDiscountAmount + $this->_pointHelper->getPointMoneyEquivalence($pointsToBeApplied, true, $quote, $quote->getStoreId());
            $discountAmount = $this->priceCurrency->convert($basePointDiscountAmount);
            
            $item->setRewardDiscountAmount($rewardDiscountAmount);
            $item->setBaseRewardDiscountAmount($baseRewardDiscountAmount);

            $item->setDiscountAmount($this->priceCurrency->round($discountAmount));
            $item->setBaseDiscountAmount($this->priceCurrency->round($basePointDiscountAmount));
            $pointsUsed -= $pointsToBeApplied;

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                //$points = $this->_calculator->process($item, $points);
                $this->distributeDiscount($item, $quote);
                foreach ($item->getChildren() as $child) {
                    $eventArgs['item'] = $child;
                    $this->_eventManager->dispatch('rewardpoints_quote_address_discount_item', $eventArgs);
                    $this->aggregateItemDiscount($child, $total);
                }
            } else {
                $this->aggregateItemDiscount($item, $total);
            }
        }

        /**
         * Process shipping amount discount
         */
        /* TODO
         * $address->setShippingDiscountAmount(0);
            $address->setBaseShippingDiscountAmount(0);
            if ($address->getShippingAmount()) {
                $this->calculator->processShippingAmount($address);
                $total->addTotalAmount($this->getCode(), -$address->getShippingDiscountAmount());
                $total->addBaseTotalAmount($this->getCode(), -$address->getBaseShippingDiscountAmount());
            } */
        
        if ($pointsUsed > 0 && $address->getShippingAmount() && $rewardHelper->isShippingDiscounted($quote->getStoreId())){
            $inclTax = $this->_pointHelper->getIncludeTax($quote->getStoreId());
            $baseDiscountAmount = abs($address->getBaseShippingDiscountAmount());
            
            if ($inclTax){
                $itemPrice = $address->getBaseShippingInclTax() - $baseDiscountAmount;
            } else {
                $itemPrice = $address->getShippingAmount() - $baseDiscountAmount;
            }
            
            $itemPriceInPoints = $this->_pointHelper->getPointsProductPriceEquivalence($itemPrice, $quote->getStoreId());
            $pointsToBeApplied = min($pointsUsed, $itemPriceInPoints);
            
            $baseRewardDiscountAmount = $this->_pointHelper->getPointMoneyEquivalence($pointsToBeApplied, true, $quote, $quote->getStoreId());
            $rewardDiscountAmount = $this->priceCurrency->convert($baseRewardDiscountAmount);
            
            $total->addTotalAmount($this->getCode(), -$rewardDiscountAmount);
            $total->addBaseTotalAmount($this->getCode(), -$baseRewardDiscountAmount);
            $pointsUsed -= $pointsToBeApplied;
            $realPointUsage += $pointsToBeApplied;
        }

        //$this->_calculator->prepareDescription($address);
        $quote->setRewardpointsCartRuleText(NULL);
        $points = $rewardHelper->getAllItemsPointsValue($items, $quote);
        $quote->setRewardpointsGathered($rewardHelper->getPointMax($points, $quote->getStoreId()));
        $address->setRewardpointsGathered($points);
        
        if ($originalPointsUsed > 0 && $originalPointsUsed != $realPointUsage && $realPointUsage > 0){
            $quote->setRewardpointsReferrer(null);
            $quote->setRewardpointsQuantity($realPointUsage);
            $points_value = $rewardHelper->getPointMoneyEquivalence($quote->getRewardpointsQuantity(), true, $quote, $quote->getStoreId());
            $quote->setBaseRewardpoints($points_value);
            $quote->setRewardpoints($this->priceCurrency->convert($points_value));
        }
        
        
        //$total->setSubtotalWithDiscount($total->getSubtotal() + $total->getDiscountAmount());
        //$total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $total->getBaseDiscountAmount());

        return $this;
    }

    protected function getMaxPointUsage($address, $points) {
        if ($points > 0) {
            $customerPoints = $this->getCustomerPoints($address);
            $points = min($points, $customerPoints);
        }

        $points = $this->_pointHelper->getMaxOrderUsage($address->getQuote(), $points, true, $address->getQuote()->getStoreId());
        return $points;
    }

    /**
     * Aggregate item discount information to address data and related properties
     *
     * @param AbstractItem $item
     * @return $this
     */
    //protected function _aggregateItemDiscount($item) {
    protected function aggregateItemDiscount(
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        \Magento\Quote\Model\Quote\Address\Total $total
    ){
        
        $total->addTotalAmount($this->getCode(), -$item->getRewardDiscountAmount());
        $total->addBaseTotalAmount($this->getCode(), -$item->getBaseRewardDiscountAmount());
        
        //$this->_addAmount(-$item->getRewardDiscountAmount());
        //$this->_addBaseAmount(-$item->getBaseRewardDiscountAmount());
        return $this;
    }

    /**
     * Distribute discount at parent item to children items
     *
     * @param AbstractItem $item
     * @return $this
     */
    protected function distributeDiscount(\Magento\Quote\Model\Quote\Item\AbstractItem $item, $quote) {
        $inclTax = $this->_pointHelper->getIncludeTax($quote->getStoreId());
        
	$baseDiscountAmount = 0;
	if ($inclTax){
            $parentBaseRowTotal = $item->getBaseRowTotalInclTax() - $baseDiscountAmount;
        } else {
            $parentBaseRowTotal = $item->getBaseRowTotal();
        }
        $keys = [
            'discount_amount',
            'base_discount_amount',
            'original_discount_amount',
            'base_original_discount_amount',
        ];
        $roundingDelta = [];
        foreach ($keys as $key) {
            //Initialize the rounding delta to a tiny number to avoid floating point precision problem
            $roundingDelta[$key] = 0.0000001;
        }
        foreach ($item->getChildren() as $child) {
            $ratio = $child->getBaseRowTotal() / $parentBaseRowTotal;
            foreach ($keys as $key) {
                if (!$item->hasData($key)) {
                    continue;
                }
                $value = $item->getData($key) * $ratio;
                $roundedValue = $this->priceCurrency->round($value + $roundingDelta[$key]);
                $roundingDelta[$key] += $value - $roundedValue;
                $child->setData($key, $roundedValue);
            }
        }

        foreach ($keys as $key) {
            $item->setData($key, 0);
        }
        return $this;
    }

    protected function clearValues(Address\Total $total) {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * Add discount total information to address
     *
     * @param Address $address
     * @return $this
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total) {
        //public function fetch(Address $address)
        $result = null;
        $amount = $quote->getRewardpoints();
        
        if ($amount != 0) {
            
            //if ($amount != 0 && $address->getAddressType() == 'billing') {
            $description = __('Reward Points');
            $title = __('Reward Points');
            /* if (strlen($description)) {
              $title = __('Reward Points (%1)', $description);
              } */
            //$address->addTotal(['code' => $this->getCode(), 'title' => $title, 'value' => -$amount]);
            $result = [
                'code' => $this->getCode(),
                'title' => $title,
                'value' => -$amount
            ];
        }
        return $result;
    }

    public function merge_fetch(Address $address) {
        $amount = $address->getDiscountAmount();
        $amountPoints = -$address->getQuote()->getRewardpoints();

        if ($amount != 0 || $amountPoints != 0) {
            $description = $address->getDiscountDescription();
            $titleRewards = '';
            if ($address->getQuote()->getRewardpointsQuantity() > 0) {
                $titleRewards = __('Reward Points (%1 points)', $this->priceCurrency->round($address->getQuote()->getRewardpointsQuantity()));
                //if ($description) $description .= ', ';
                //$description .= $titleRewards;
            }

            $title = '';
            if ($amount != 0) {
                $title = __('Discount');
                if (strlen($description)) {
                    $title = __('Discount (%1)', $description);
                }
            }


            if ($titleRewards && $title) {
                $title = __('%1 / %2', $titleRewards, $title);
            }
            //$address->addTotal(['code' => $this->getCode(), 'title' => $title, 'value' => $amount]);
            $address->addTotal(['code' => 'discount', 'title' => $title, 'value' => $amount + $amountPoints]);
        }
        return $this;
    }

}
