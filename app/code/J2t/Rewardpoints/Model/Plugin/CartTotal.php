<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model\Plugin;

use Magento\Persistent\Helper\Session as PersistentSession;
use Magento\Persistent\Helper\Data as PersistentHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Customer\Model\Session as CustomerSession;

class CartTotal {

    /**
     * @var PersistentSession
     */
    private $persistentSession;

    /**
     * @var PersistentHelper
     */
    private $persistentHelper;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param PersistentHelper $persistentHelper
     * @param PersistentSession $persistentSession
     * @param CheckoutSession $checkoutSession
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
    PersistentHelper $persistentHelper, PersistentSession $persistentSession, CheckoutSession $checkoutSession, QuoteIdMaskFactory $quoteIdMaskFactory, CustomerSession $customerSession
    ) {
        $this->persistentHelper = $persistentHelper;
        $this->persistentSession = $persistentSession;
        $this->checkoutSession = $checkoutSession;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(\Magento\Quote\Model\Cart\CartTotalRepository $subject, \Magento\Quote\Model\Cart\Totals $result) {
        $quote = $this->checkoutSession->getQuote();

        $result->setData('base_rewardpoints', -$quote->getData('base_rewardpoints'));
        $result->setData('rewardpoints_quantity', $quote->getData('rewardpoints_quantity'));

        $result->setData('rewardpoints', $quote->getData('rewardpoints'));
        $result->setData('rewardpoints_gathered', $quote->getData('rewardpoints_gathered'));
        $result->setData('rewardpoints_used', $quote->getData('rewardpoints_used'));

        return $result;
    }

}
