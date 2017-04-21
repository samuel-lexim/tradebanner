<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Cart;

/**
 * Grand Total Tax Details Model
 */
class BaseRewardpoints extends \Magento\Framework\Model\AbstractExtensibleModel implements
	\J2t\Rewardpoints\Api\Data\BaseRewardpointsInterface
{
	/**
     * Get grand total in base currency
     *
     * @return float|null
     */
	public function getBaseRewardpoints()
    {
        return $this->getData('base_rewardpoints');
    }

    /**
     * Set grand total in quote currency
     *
     * @param float $grandTotal
     * @return $this
     */
    public function setBaseRewardpoints($baseRewardpoints)
    {
        return $this->setData('base_rewardpoints', $baseRewardpoints);
    }
	
	/**
     * Get reward points qty in base currency
     *
     * @return float|null
     */
	public function getRewardpointsQuantity()
    {
        return $this->getData('rewardpoints_quantity');
    }

    /**
     * Set reward points qty in quote currency
     *
     * @param float $rewardpointsQuantity
     * @return $this
     */
    public function setRewardpointsQuantity($rewardpointsQuantity)
    {
        return $this->setData('rewardpoints_quantity', $rewardpointsQuantity);
    }
	
	/**
     * {@inheritdoc}
     *
     * @return \Magento\Quote\Api\Data\TotalsExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Quote\Api\Data\TotalsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\Quote\Api\Data\TotalsExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
	
}
