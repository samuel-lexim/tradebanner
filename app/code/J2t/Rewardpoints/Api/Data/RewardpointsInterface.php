<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Api\Data;

interface RewardpointsInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    /*     * #@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */

    const KEY_BASE_REWARDPOINTS = 'base_rewardpoints';
    const KEY_REWARDPOINTS_QUANTITY = 'rewardpoints_quantity';

    /*     * #@- */

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

    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\GiftMessage\Api\Data\MessageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
    \J2t\Rewardpoints\Api\Data\RewardpointsExtensionInterface $extensionAttributes
    );
}
