<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Sales\Order;

use Magento\Sales\Model\Order;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Points extends \Magento\Framework\View\Element\Template {

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\Object
     */
    protected $_source;
    protected $priceCurrency;
    protected $pricingHelper;

    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, PriceCurrencyInterface $priceCurrency, \Magento\Framework\Pricing\Helper\Data $pricingHelper, array $data = []
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    public function getSource() {
        return $this->_source;
    }

    public function initTotals() {
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        //if ($this->_source->getRewardpointsUsed() > 0 || $this->_source->getRewardpointsGathered() > 0){
        $this->_addRewardpoints();


        //}
        return $this;
    }

    protected function _addRewardpoints($after = 'discount') {
        $discountTotal = $this->getParentBlock()->getTotal('discount');

        if (is_object($this->getParentBlock()->getInvoice()) && is_object($discountTotal) && (abs($discountTotal->getData('base_value')) || abs($discountTotal->getData('value')) ) && $this->_source->getRewardpointsQuantity()) {
            $discountTotal->setData('label', __('%1 - including usage of %2 point(s)', $discountTotal->getData('label'), $this->getRewardpointsUsedText()));
        }

        $gatheredTotal = new \Magento\Framework\DataObject(['code' => 'rewardpoints', 'block_name' => $this->getNameInLayout()]);
        $this->getParentBlock()->addTotal($gatheredTotal, $after);
        return $this;
    }

    public function getStore() {
        return $this->_order->getStore();
    }

    public function getPointsGathered() {
        $source = $this->getSource();
        return $this->priceCurrency->round($source->getRewardpointsGathered()); // * 1;
    }

    public function getPointsUsed() {
        $source = $this->getSource();
        return $this->pricingHelper->currency(
                        -$source->getBaseRewardpoints()
        );
        //return $this->priceCurrency->round($source->getBaseRewardpoints());// * 1;
    }

    public function getRewardpointsUsedText() {
        $source = $this->getSource();
        return $this->priceCurrency->round($source->getRewardpointsQuantity()); // * 1;
    }

}
