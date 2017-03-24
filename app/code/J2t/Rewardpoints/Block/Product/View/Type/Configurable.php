<?php

/**
 * Catalog super product configurable part block
 *
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Product\View\Type;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Configurable extends \Magento\Catalog\Block\Product\View\AbstractView {

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogProduct = null;

    /**
     * Current customer
     *
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Prices
     *
     * @var array
     */
    protected $_prices = [];

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\ConfigurableProduct\Helper\Data $imageHelper
     */
    protected $helper;
    protected $_pointData;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\ConfigurableProduct\Helper\Data $helper
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param CurrentCustomer $currentCustomer
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Stdlib\ArrayUtils $arrayUtils, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\ConfigurableProduct\Helper\Data $helper, \Magento\Catalog\Helper\Product $catalogProduct,
    //\J2t\Rewardpoints\Helper\Data $pointHelper,
            CurrentCustomer $currentCustomer, PriceCurrencyInterface $priceCurrency, array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        $this->catalogProduct = $catalogProduct;
        $this->currentCustomer = $currentCustomer;
        //$this->_pointData = $pointHelper;
        parent::__construct(
                $context, $arrayUtils, $data
        );
    }

    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAllowAttributes() {
        return $this->getProduct()->getTypeInstance()->getConfigurableAttributes($this->getProduct());
    }

    /**
     * Check if allowed attributes have options
     *
     * @return bool
     */
    public function hasOptions() {
        $attributes = $this->getAllowAttributes();
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
                if ($attribute->getData('prices')) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts() {
        if (!$this->hasAllowProducts()) {
            $products = [];
            $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();
            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
            foreach ($allProducts as $product) {
                if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    /**
     * Retrieve current store
     *
     * @return \Magento\Store\Model\Store
     */
    public function getCurrentStore() {
        return $this->_storeManager->getStore();
    }

    /**
     * Returns additional values for js config, con be overridden by descendants
     *
     * @return array
     */
    protected function _getAdditionalConfig() {
        return [];
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig() {
        //$store = $this->getCurrentStore();
        $currentProduct = $this->getProduct();

        //$regularPrice = $currentProduct->getPriceInfo()->getPrice('regular_price');
        //$finalPrice = $currentProduct->getPriceInfo()->getPrice('final_price');
        $options = \Magento\Framework\App\ObjectManager::getInstance()->get('J2t\Rewardpoints\Helper\Data')->getConfigurableOptions($currentProduct, $this->getAllowProducts());

        $config = [
            'points' => $options['points'],
            'equivalence' => $options['equivalence'],
            'default_points' => $options['basePoints'],
            'basePoints' => $options['basePoints'],
            'baseEquivalence' => $options['basePoints'],
            'productId' => $currentProduct->getId(),
            'equivalenceText' => __("%1 points = %2.", '{{points}}', '{{equivalence}}')
        ];

        if ($currentProduct->hasPreconfiguredValues() && !empty($attributes['defaultValues'])) {
            $config['defaultValues'] = $attributes['defaultValues'];
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price) {
        return str_replace(',', '.', $price);
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @param bool $round
     * @return float
     */
    protected function _convertPrice($price, $round = false) {
        if (empty($price)) {
            return 0;
        }

        $price = $this->priceCurrency->convert($price);
        if ($round) {
            $price = $this->priceCurrency->round($price);
        }

        return $price;
    }
}
