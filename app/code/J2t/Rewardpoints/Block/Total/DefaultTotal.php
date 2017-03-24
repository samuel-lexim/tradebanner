<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
//namespace Magento\Checkout\Block\Total;
namespace J2t\Rewardpoints\Block\Total;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Default Total Row Renderer
 */
class DefaultTotal extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * @var string
     */
    //protected $_template = 'Magento_Checkout::total/default.phtml';
	protected $_template = 'J2t_Rewardpoints::total/default.phtml';

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

	protected $priceCurrency;
	protected $_pointHelper = null;
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
		PriceCurrencyInterface $priceCurrency,
		\J2t\Rewardpoints\Helper\Data $salesPointHelper,
        array $data = []
    ) {
		$this->priceCurrency = $priceCurrency;
		$this->_pointHelper = $salesPointHelper;
        parent::__construct($context, $customerSession, $checkoutSession, $salesConfig, $data);
    }
	
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_store = $this->_storeManager->getStore();
    }
	
	public function getUsedPoints()
	{
		return $this->priceCurrency->round($this->getQuote()->getRewardpointsQuantity());
	}
	
	public function getShowRemoveLink()
	{
		return $this->_pointHelper->getShowRemoveLink();
	}

    /**
     * Get style assigned to total object
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->getTotal()->getStyle();
    }

    /**
     * @param float $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->setData('total', $total);
        if ($total->getAddress()) {
            $this->_store = $total->getAddress()->getQuote()->getStore();
        }
        return $this;
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_store;
    }
}
