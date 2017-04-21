<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model;

use J2t\Rewardpoints\Api\RewardpointsManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Rewardpoints management object.
 */
class RewardpointsManagement implements RewardpointsManagementInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository Quote repository.
     */
    public function __construct(
        //\Magento\Quote\Model\QuoteRepository $quoteRepository
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        return $quote->getRewardpointsQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $rewardpointsQuantity)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $quote->setRewardpointsQuantity($rewardpointsQuantity);
            $this->quoteRepository->save($quote->collectTotals());
            //throw new InputException(__('Shipping method is not applicable for empty cart'));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to redeem points'));
        }
		if (!$quote->getRewardpointsQuantity()) {
            throw new NoSuchEntityException(__('Unable to redeem points'));
        }
        /*if ($quote->getRewardpointsQuantity() != $rewardpointsQuantity) {
            throw new NoSuchEntityException(__('Coupon code is not valid'));
        }*/
        //return true;
        return $quote->getRewardpointsQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $quote->setRewardpointsQuantity('');
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not remove points'));
        }
        if ($quote->getRewardpointsQuantity() != '') {
            throw new CouldNotDeleteException(__('Could not remove points'));
        }
        return true;
    }
}
