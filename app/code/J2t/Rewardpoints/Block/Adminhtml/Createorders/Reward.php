<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Createorders;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Reward extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    protected $_pointData = null;
	
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \J2t\Rewardpoints\Helper\Data $pointHelper,
        array $data = []
    ) {
        $this->_pointData = $pointHelper;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }
	
	public function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_reward_form');
    }
	
	/**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Reward Points');
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-rewardpoints-quote';
    }

    public function getPointsUsed()
    {
        return $this->priceCurrency->round($this->getQuote()->getRewardpointsQuantity());
    }

    public function getPointsOnOrder() {
        return $this->priceCurrency->round($this->getQuote()->getRewardpointsGathered());
    }

    public function getClientPoints()
    {
        if (($customerId = $this->getQuote()->getCustomerId()) && ($storeId = $this->getQuote()->getStoreId())){
                return $this->_pointData->getCurrentCustomerPoints($customerId, $storeId);
        }
        return 0;
    }
}

