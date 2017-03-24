<?php

namespace J2t\Rewardpoints\Block;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Rewardcoupon extends \Magento\Checkout\Block\Cart\AbstractCart {

    protected $_template = 'reward_coupon.phtml';
    protected $_pointData;
    protected $priceCurrency;
    protected $_customerPoints;
    protected $_coreHelper;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Checkout\Model\Session $checkoutSession, \J2t\Rewardpoints\Helper\Data $pointHelper, PriceCurrencyInterface $priceCurrency, \Magento\Framework\Json\Helper\Data $coreHelper, array $data = []
    ) {
        $this->_coreHelper = $coreHelper;
        $this->_pointData = $pointHelper;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
    }

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getQuoteCartRuleText() {
        $details_items_line = array();
        $items = $this->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                
            } else {
                //if ($item->getRewardpointsGathered() && ($quote_text_rule_point = unserialize($item->getRewardpointsGathered()))){
                if ($item->getRewardpointsCartRuleText() && ($quote_text_rule_point = unserialize($item->getRewardpointsCartRuleText()))) {
                    foreach ($quote_text_rule_point as $rule_point_text) {
                        $details_items_line[] = $rule_point_text;
                    }
                }
            }
        }
        if ($this->getQuote()->getRewardpointsCartRuleText() && $quote_text_rule_point = unserialize($this->getQuote()->getRewardpointsCartRuleText())) {
            foreach ($quote_text_rule_point as $rule_point_text) {
                $details_items_line[] = $rule_point_text;
            }
        }
        return $details_items_line;
    }

    public function isCustomerLogged() {
        return $this->_customerSession->isLoggedIn();
    }

    public function getMinCustomerPointsBalance() {
        return $this->_pointData->getMinPointBalance();
    }

    public function getMaxOrderUsage($points) {
        return $this->_pointData->getMaxOrderUsage($this->getQuote(), $points, true);
    }

    public function getCartPoints() {
        return $this->priceCurrency->round($this->getQuote()->getRewardpointsGathered());
    }

    public function getCartUsedPoints() {
        return $this->priceCurrency->round($this->getQuote()->getRewardpointsQuantity());
    }

    public function getCurrentCustomerPoints() {
        if (!$this->_customerPoints) {
            $this->_customerPoints = $this->_pointData->getCurrentCustomerPoints();
        }
        return $this->priceCurrency->round($this->_customerPoints);
    }

    public function showEquivalence() {
        return $this->_pointData->getShowEquivalence();
    }

    public function getPointsEquivalence($points) {
        return $this->_pointData->getPointMoneyEquivalence($points);
    }

    public function getStepValue() {
        return $this->_pointData->getStepValue();
    }

    public function getButtonRemove() {
        $returnValue = ($this->_pointData->getShowRemoveLink()) ? 'true' : 'false';
        return $returnValue;
    }
    
    public function getStepMultiplier() {
        return $this->_pointData->getStepMultiplier();
    }

    public function getStepSlider() {
        return $this->_pointData->getStepSlider();
    }

    public function getMaxApplyValue($stepValue = 0) {
        $points = $this->getCurrentCustomerPoints();
        if ($stepValue) {
            $pointsSteps = $points / $stepValue;
            $points = $stepValue * floor($pointsSteps);
        }
        return $points;
    }
    
    public function isActive() {
        if (!$this->_pointData->getActive()){
            return 'false';
        }
        return 'true';
    }

    public function getStepValues($returnArray = false) {
        $stepValue = $this->getStepValue();        
        $currentStepValue = $this->getStepValue();
        
        $points = $this->getMaxOrderUsage($this->getCurrentCustomerPoints());
        $max = $this->getMaxApplyValue($stepValue);
        $stepMultiplier = $this->getStepMultiplier();
        
        $return = array();

        if ($currentStepValue) {
            while ($currentStepValue <= $points) {
                if ($currentStepValue <= $points)
                    $return[] = $currentStepValue;
                $currentStepValue = ($stepMultiplier > 1) ? ($currentStepValue * $stepMultiplier) : $stepValue + $currentStepValue;
            }
        }



        if ($returnArray) {
            return $return;
        }

        return $this->_coreHelper->jsonEncode($return);
    }

    public function showDetails() {
        return $this->_pointData->showDetails();
    }

    public function showImage() {
        return $this->_pointData->getShowBigImage();
    }

    /* public function getItemPoints() {
      return $this->_pointData->getShowBigImage();
      } */

    public function sizeImage() {
        return $this->_pointData->getSizeBigImage();
    }

    public function getImageUrl() {
        return $this->_pointData->getURLBigImage();
    }

}
