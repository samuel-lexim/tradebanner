<?php

/**
 * Catalog super product configurable part block
 *
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Product\View\Type;

use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;

class Bundle extends \Magento\Catalog\Block\Product\View\AbstractView {

    protected $options;
    protected $catalogProduct;
    protected $productPriceFactory;
    protected $jsonEncoder;
    protected $localeFormat;
    private $selectedOptions = [];

    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Stdlib\ArrayUtils $arrayUtils, \Magento\Catalog\Helper\Product $catalogProduct, \Magento\Bundle\Model\Product\PriceFactory $productPrice, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Framework\Locale\FormatInterface $localeFormat, array $data = []
    ) {
        $this->catalogProduct = $catalogProduct;
        $this->productPriceFactory = $productPrice;
        $this->jsonEncoder = $jsonEncoder;
        $this->localeFormat = $localeFormat;
        parent::__construct(
                $context, $arrayUtils, $data
        );
    }

    public function getOptions() {
        if (!$this->options) {
            $product = $this->getProduct();
            $typeInstance = $product->getTypeInstance();
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $typeInstance->getOptionsCollection($product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                    $typeInstance->getOptionsIds($product), $product
            );

            $this->options = $optionCollection->appendSelections(
                    $selectionCollection, false, $this->catalogProduct->getSkipSaleableCheck()
            );
        }

        return $this->options;
    }

    private function getSelectionItemData(Product $product, Product $selection) {
        $qty = ($selection->getSelectionQty() * 1) ? : '1';

        $optionPriceAmount = $product->getPriceInfo()
                ->getPrice('bundle_option')
                ->getOptionSelectionAmount($selection);
        $finalPrice = $optionPriceAmount->getValue();
        $basePrice = $optionPriceAmount->getBaseAmount();

        $selection = [
            'qty' => $qty,
            'customQty' => $selection->getSelectionCanChangeQty(),
            'points' => \Magento\Framework\App\ObjectManager::getInstance()->get('J2t\Rewardpoints\Helper\Data')->getProductPoints($selection),
            'canApplyMsrp' => false
        ];
        return $selection;
    }

    private function getTierPrices(Product $product, Product $selection) {
        // recalculate currency
        $tierPrices = $selection->getPriceInfo()
                ->getPrice(\Magento\Catalog\Pricing\Price\TierPrice::PRICE_CODE)
                ->getTierPriceList();

        foreach ($tierPrices as &$tierPriceInfo) {

            $price = $tierPriceInfo['price'];

            $priceBaseAmount = $price->getBaseAmount();
            $priceValue = $price->getValue();

            $bundleProductPrice = $this->productPriceFactory->create();
            $priceBaseAmount = $bundleProductPrice->getLowestPrice($product, $priceBaseAmount);
            $priceValue = $bundleProductPrice->getLowestPrice($product, $priceValue);

            $tierPriceInfo['prices'] = [
                'oldPrice' => [
                    'amount' => $priceBaseAmount
                ],
                'basePrice' => [
                    'amount' => $priceBaseAmount
                ],
                'finalPrice' => [
                    'amount' => $priceValue
                ]
            ];
        }
        return $tierPrices;
    }

    private function getSelections(Option $option, Product $product) {
        $selections = [];
        $selectionCount = count($option->getSelections());
        foreach ($option->getSelections() as $selectionItem) {

            $selectionId = $selectionItem->getSelectionId();
            $selections[$selectionId] = $this->getSelectionItemData($product, $selectionItem);

            if (($selectionItem->getIsDefault() || $selectionCount == 1 && $option->getRequired()) && $selectionItem->isSalable()
            ) {
                $this->selectedOptions[$option->getId()][] = $selectionId;
            }
        }
        return $selections;
    }

    private function getOptionItemData(Option $option, Product $product, $position) {
        return [
            'selections' => $this->getSelections($option, $product),
            'title' => $option->getTitle(),
            'isMulti' => in_array($option->getType(), ['multi', 'checkbox']),
            'position' => $position
        ];
    }

    private function getConfigData(Product $product, array $options) {
        $isFixedPrice = $this->getProduct()->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED;

        $productAmount = $product
                ->getPriceInfo()
                ->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)
                ->getPriceWithoutOption();

        $baseProductAmount = $product
                ->getPriceInfo()
                ->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)
                ->getAmount();

        $config = [
            'options' => $options,
            'selected' => $this->selectedOptions,
            'bundleId' => $product->getId(),
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $isFixedPrice ? $baseProductAmount->getValue() : 0
                ],
                'basePrice' => [
                    'amount' => $isFixedPrice ? $productAmount->getBaseAmount() : 0
                ],
                'finalPrice' => [
                    'amount' => $isFixedPrice ? $productAmount->getValue() : 0
                ]
            ],
            'equivalenceText' => __("%1 points = %2.", '{{points}}', '{{equivalence}}'),
            'baseEquivalence' => \Magento\Framework\App\ObjectManager::getInstance()->get('J2t\Rewardpoints\Helper\Data')->getPointDiscountRequiredValue(),
            'priceType' => $product->getPriceType(),
            'isFixedPrice' => $isFixedPrice,
        ];

        return $config;
    }

    public function getJsonConfig() {

        $optionsArray = $this->getOptions();
        $options = [];
        $currentProduct = $this->getProduct();

        $defaultValues = [];
        $preConfiguredFlag = $currentProduct->hasPreconfiguredValues();

        $preConfiguredValues = $preConfiguredFlag ? $currentProduct->getPreconfiguredValues() : null;

        $position = 0;
        foreach ($optionsArray as $optionItem) {

            if (!$optionItem->getSelections()) {
                continue;
            }
            $optionId = $optionItem->getId();
            $options[$optionId] = $this->getOptionItemData($optionItem, $currentProduct, $position);

            // Add attribute default value (if set)
            if ($preConfiguredFlag) {
                $configValue = $preConfiguredValues->getData('bundle_option/' . $optionId);
                if ($configValue) {
                    $defaultValues[$optionId] = $configValue;
                }
            }
            $position++;
        }
        $config = $this->getConfigData($currentProduct, $options);

        if ($preConfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        return $this->jsonEncoder->encode($config);
    }

}
