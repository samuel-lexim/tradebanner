<?php
/**
 *
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Controller\Cart;

class RewardPost extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Store\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        //CustomerCart $cart,
		\Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Initialize coupon
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->cart->getQuote()->getItemsCount()) {
            return $this->_goBack();
        }

        
		$points = $this->getRequest()->getParam('rewardpoints_value');
		$removePoints = $this->getRequest()->getParam('remove');
		
		
        if (!strlen($points) && !is_numeric($points)) {
            return $this->_goBack();
        }

        try {
            $this->cart->getQuote()->getShippingAddress()->setCollectShippingRates(true);
			//$this->cart->getQuote()->setRewardpointsQuantity($points);
			if ($removePoints){
				$points = 0;
			}
			
            $this->cart->getQuote()->setRewardpointsQuantity($points)->collectTotals();
            $this->quoteRepository->save($this->cart->getQuote());

            if ($this->cart->getQuote()->getRewardpointsQuantity()) {
				$this->messageManager->addSuccess(
					__(
						'You\'ve successfully applied %1 point(s).',
						$this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($this->cart->getQuote()->getRewardpointsQuantity())
					)
				);
                
            } else {
                $this->messageManager->addSuccess(__('Points usage cancelled.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Unable to use your points.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return $this->_goBack();
    }
}
