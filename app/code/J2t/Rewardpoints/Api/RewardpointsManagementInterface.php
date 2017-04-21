<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Api;

/**
 * Rewardpoints management service interface.
 * @api
 */
interface RewardpointsManagementInterface
{
    /**
     * Returns information for rewardpoints in a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return string The rewardpoints data.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function get($cartId);

    /**
     * Adds point discount to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $rewardpointsQuantity The rewardpoints data.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified amount of points could not be added.
     */
    public function set($cartId, $rewardpointsQuantity);

    /**
     * Deletes point discount from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException Points could not be removed.
     */
    public function remove($cartId);
}
