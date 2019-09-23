<?php

namespace FME\Pricecalculator\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_scopeConfig;
    protected $productFactory;
    protected $logger;

    const XML_PATH_ENABLED = 'pricecalculator/general/enable_in_frontend';
    const XML_PATH_FIELDS_LABEL = 'pricecalculator/basic/fields_label';
    const XML_PATH_KEYWORD_MIN = 'pricecalculator/basic/keyword_min';
    const XML_PATH_KEYWORD_MAX = 'pricecalculator/basic/keyword_max';

    const XML_DISCOUNT_TITLE = 'pricecalculator/basic/discount_title';

    const XML_SHOW_BASE_PRICE = 'pricecalculator/product_page/show_basic_price';
    const XML_SHOW_DISCOUNT_PRICE = 'pricecalculator/product_page/show_discount_price';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->productFactory = $productFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * check the module is enabled, frontend
     * @return bool
     */
    public function isEnabledInFrontend()
    {
        $isEnabled = true;
        $enabled = $this->_scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
        if ($enabled == NULL || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

    public function getFirstLabel()
    {
        //length is the longest line
        $fieldLabelsConfig = $this->_scopeConfig->getValue(self::XML_PATH_FIELDS_LABEL, ScopeInterface::SCOPE_STORE);
        $parts = explode(';', $fieldLabelsConfig);
        return (isset($parts[0])) ? $parts[0] : false;
    }

    public function getSecondLabel()
    {

        $fieldLabelsConfig = $this->_scopeConfig->getValue(self::XML_PATH_FIELDS_LABEL, ScopeInterface::SCOPE_STORE);
        $parts = explode(';', $fieldLabelsConfig);
        return (isset($parts[1])) ? $parts[1] : false;
    }

    public function getThirdLabel()
    {

        $fieldLabelsConfig = $this->_scopeConfig->getValue(self::XML_PATH_FIELDS_LABEL, ScopeInterface::SCOPE_STORE);
        $parts = explode(';', $fieldLabelsConfig);
        return (isset($parts[2])) ? $parts[2] : false;
    }

    public function getMinKeyword()
    {
        $keyword = $this->_scopeConfig->getValue(self::XML_PATH_KEYWORD_MIN, ScopeInterface::SCOPE_STORE);

        if ($keyword == NULL) {
            $keyword = 'min';
        }

        return $keyword;
    }

    public function getMaxKeyword()
    {
        $keyword = $this->_scopeConfig->getValue(self::XML_PATH_KEYWORD_MAX, ScopeInterface::SCOPE_STORE);

        if ($keyword == NULL) {
            $keyword = 'max';
        }

        return $keyword;
    }

    public function getDiscountTitle()
    {

        $title = $this->_scopeConfig->getValue(self::XML_DISCOUNT_TITLE, ScopeInterface::SCOPE_STORE);
        return $title ? $title : 'Discount';
    }

    public function showBasePrice()
    {

        return $this->_scopeConfig->getValue(self::XML_SHOW_BASE_PRICE, ScopeInterface::SCOPE_STORE);
    }

    public function showDiscountPrice()
    {

        return $this->_scopeConfig->getValue(self::XML_SHOW_DISCOUNT_PRICE, ScopeInterface::SCOPE_STORE);
    }

    public function getFieldOptions($product)
    {
        /**
         * ['label' => ['label_{min}', 'label_{max}']]
         * min max will be checked against product attributes
         * for price calculator
         */
        $options = [];
        $min = $this->getMinKeyword();
        $max = $this->getMaxKeyword();

        $fieldLabelsConfig = $this->_scopeConfig->getValue(self::XML_PATH_FIELDS_LABEL, ScopeInterface::SCOPE_STORE);
        $parts = explode(';', $fieldLabelsConfig);

        $limit = null;
        if ($product->getPricingLimit()) {
            $limit = explode(';', $product->getPricingLimit());
        }

        foreach ($parts as $label) {
            $lookMin = $label . '_' . $min;
            $lookMax = $label . '_' . $max;

            foreach ($limit as $i) {
                $item = explode('=', $i);
                if (in_array($lookMin, $item)) {
                    $options[$label]['min'] = $item[1];
                }
                if (in_array($lookMax, $item)) {
                    $options[$label]['max'] = $item[1];
                }
            }

        }

        return $options;
    }

    public function getFieldOptionsCount()
    {
        $fieldLabelsConfig = $this->_scopeConfig->getValue(self::XML_PATH_FIELDS_LABEL, ScopeInterface::SCOPE_STORE);
        $parts = explode(';', $fieldLabelsConfig);
        return count($parts);
    }


    public function getInputUnitLabel($_product)
    {
        $optionText = false;
        $optionId = $_product->getCurrentUnit();
        $attr = $_product->getResource()->getAttribute('current_unit');
        if ($attr->usesSource()) {
            $optionText = $attr->getSource()->getOptionText($optionId);
        }

        return $optionText;
    }

    public function getOutputUnitLabel($_product)
    {
        $optionText = false;
        $optionId = $_product->getOutputUnit();
        $attr = $_product->getResource()->getAttribute('output_unit');
        if ($attr->usesSource()) {
            $optionText = $attr->getSource()->getOptionText($optionId);
        }

        return $optionText;
    }


    public function unitConversion($input, $output)
    {
        if ($input == 'Centi-Meter') {
            return $this->convertFromCM($output);
        } else if ($input == 'Foot') {
            return $this->convertFromFoot($output);
        } else if ($input == 'Inch') {
            return $this->convertFromInch($output);
        } else if ($input == 'Meter') {
            return $this->convertFromMeter($output);
        } else if ($input == 'Milli-Meter') {
            return $this->convertFromMM($output);
        }
        return false;
    }


    public function convertFromCM($output)
    {
        if ($output == 'Centi-Meter') {
            return 1;
        } else if ($output == 'Foot') {
            return 0.0328084;
        } else if ($output == 'Inch') {
            return 0.393701;
        } else if ($output == 'Meter') {
            return 0.01;
        } else if ($output == 'Milli-Meter') {
            return 10;
        } else {
            return 1;
        }

    }


    public function convertFromFoot($output)
    {
        if ($output == 'Centi-Meter') {
            return 30.48;
        } else if ($output == 'Foot') {
            return 1;
        } else if ($output == 'Inch') {
            return 12;
        } else if ($output == 'Meter') {
            return 0.3048;
        } else if ($output == 'Milli-Meter') {
            return 304.8;
        } else {
            return 1;
        }

    }


    public function convertFromInch($output)
    {
        if ($output == 'Centi-Meter') {
            return 2.54;
        } else if ($output == 'Foot') {
            return 0.0833333;
        } else if ($output == 'Inch') {
            return 1;
        } else if ($output == 'Meter') {
            return 0.0254;
        } else if ($output == 'Milli-Meter') {
            return 25.4;
        } else {
            return 1;
        }

    }


    public function convertFromMeter($output)
    {
        if ($output == 'Centi-Meter') {
            return 100;
        } else if ($output == 'Foot') {
            return 3.28084;
        } else if ($output == 'Inch') {
            return 39.3701;
        } else if ($output == 'Meter') {
            return 1;
        } else if ($output == 'Milli-Meter') {
            return 1000;
        } else {
            return 1;
        }

    }


    public function convertFromMM($output)
    {
        if ($output == 'Centi-Meter') {
            return 0.1;
        } else if ($output == 'Foot') {
            return 0.00328084;
        } else if ($output == 'Inch') {
            return 0.0393701;
        } else if ($output == 'Meter') {
            return 0.001;
        } else if ($output == 'Milli-Meter') {
            return 1;
        } else {
            return 1;
        }

    }

    public function getProductPricingRule($product = null)
    {
        if ($product == null) {
            $product = $this->getProduct();
        }
        
        $pricingRule = [];
        $_priceRule = $product->getPricingRule();
        if (is_null($_priceRule)) {
            $_priceRule = $this->productFactory->create()->load($product->getId())->getPricingRule();
        }
        $data = explode(';', $_priceRule);

        foreach ($data as $item) {

            preg_match_all("/ ([^=]+) = ([^\\s]+) /x", $item, $p);
            $pair = array_combine($p[1], $p[2]);

            if (isset($pair['discount'])) {
                $pricingRule['discount'] = explode(',', $pair['discount']);
            }

            if (isset($pair['size'])) {
                $pricingRule['size'] = explode(',', $pair['size']);
            }
        }

        //area or volume
        if (in_array('area', $data)) {
            $pricingRule['by'] = 'area';
        }
        if (in_array('volume', $data)) {
            $pricingRule['by'] = 'volume';
        }
        // discount type
        if (in_array('percent', $data)) {
            $pricingRule['type'] = 'percent';
        }
        if (in_array('fixed', $data)) {
            $pricingRule['type'] = 'fixed';
        }

        return $pricingRule;
    }
}
