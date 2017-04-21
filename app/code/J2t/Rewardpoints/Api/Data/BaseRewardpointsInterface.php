<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Api\Data;


interface BaseRewardpointsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get rewardpoints discount in quote currency
     *
     * @return float|null
     */
	public function getBaseRewardpoints();
	
	/**
     * Set rewardpoints discount in quote currency
     *
     * @param float $baseRewardpoints
     * @return $this
     */
	public function setBaseRewardpoints($baseRewardpoints);
	
	/**
     * Get rewardpoints qty in quote currency
     *
     * @return float|null
     */
	public function getRewardpointsQuantity();
	
	/**
     * Set rewardpoints qty in quote currency
     *
     * @param float $rewardpointsQuantity
     * @return $this
     */
	public function setRewardpointsQuantity($rewardpointsQuantity);
	
	/**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Quote\Api\Data\TotalsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Quote\Api\Data\TotalsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\Quote\Api\Data\TotalsExtensionInterface $extensionAttributes);
}
