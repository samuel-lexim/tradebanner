<?php
/**
 * EaDesgin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eadesign.ro so we can send you a copy immediately.
 *
 * @category    eadesigndev_pdfgenerator
 * @copyright   Copyright (c) 2008-2016 EaDesign by Eco Active S.R.L.
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace Eadesigndev\Opicmsppdfgenerator\Helper\Variable;

/**
 * Handles the default system data coming from the source and generates the variables
 *
 * Class Data
 * @package Eadesigndev\Pdfgenerator\Helper
 */
class DefaultVariables extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var
     * the source for the variables
     */
    public $source;

    /**
     * @var
     * the type for the variables
     */
    public $type;

    /**
     * DefaultVariables constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * @param $source
     * @param $type
     * @return $this
     */
    public function setSourceType($source, $type)
    {
        $this->source = $source;
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getSourceDefault()
    {
        if (!$this->source) {
            throw new \Exception(__('The source must be defined.'));
        }

        $data = $this->source->getData();
        $groupName = __('Source Default Variables');
        $sourceVariables = $this->getVariablesOptionArray(true, $groupName, $data, $this->type . '.');

        return $sourceVariables;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getBarcodeDefault($barcodes)
    {
        if (!$this->source) {
            throw new \Exception(__('The source must be defined.'));
        }

        $data = $this->source->getData();
        $groupName = __('Source Barcode Variables');
        $sourceVariables = $this->getBarCodeVariables(true, $groupName, $data, 'ea_barcode_', '_' . $this->type . '.', $barcodes);

        return $sourceVariables;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getDependDefault()
    {
        if (!$this->source) {
            throw new \Exception(__('The source must be defined.'));
        }

        $data = $this->source->getData();

        $variableData = [];

        foreach ($data as $dat => $val) {
            if (is_numeric($val)) {
                $variableData[$dat] = $val;
            } else {
                continue;
            }
        }

        $groupName = __('Source Depend Variables');
        $sourceVariables = $this->getDependCurrencyOptionArray(true, $groupName, $data, $this->type . '_if.');

        return $sourceVariables;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCurrencyDefault()
    {
        if (!$this->source) {
            throw new \Exception(__('The source must be defined.'));
        }

        $data = $this->source->getData();

        $variableData = [];

        foreach ($data as $dat => $val) {
            if (is_numeric($val)) {
                $variableData[$dat] = $val;
            } else {
                continue;
            }
        }

        $groupName = __('Source Currency Variables');
        $sourceVariables = $this->getDependCurrencyOptionArray(true, $groupName, $data, 'ea_' . $this->type . '.');

        return $sourceVariables;
    }

    /**
     * @param \Magento\Sales\Model\Order\Source\Item $item
     * @param $barCodes
     * @return array|bool
     */
    public function getItemsDefault($item, $barCodes)
    {
        if (!$item) {
            return false;
        }

        $this->source = $item;
        $data = $this->source->getData();

        $variableData = [];

        $groupNameVariables = __('Item Variables');
        $sourceVariables = $this->getVariablesOptionArray(true, $groupNameVariables, $data, 'item.');

        $groupNameBarcode = __('Source Barcode Variables');

        $sourceBarcodeVariables = [];
        if (!empty($barCodes)) {
            $sourceBarcodeVariables = $this->getBarCodeVariables(true, $groupNameBarcode, $data, 'ea_barcode_', '_item.', $barCodes);
        }


        foreach ($data as $dat => $val) {
            if (is_numeric($val)) {
                $variableData[$dat] = $val;
            } else {
                continue;
            }
        }

        $groupNameCurrency = __('Source Currency Variables');
        $sourceCurrencyVariables = $this->getDependCurrencyOptionArray(true, $groupNameCurrency, $data, 'ea_item.');

        $groupNameDepend = __('Source Depend Variables');
        $sourceDependVariables = $this->getDependCurrencyOptionArray(true, $groupNameDepend, $data, 'ea_item_if.');

        $standardVariables = [$sourceVariables, $sourceCurrencyVariables, $sourceDependVariables];

        return array_merge($standardVariables, $sourceBarcodeVariables);
    }

    /**
     * @param \Magento\Sales\Model\Order\Source\Item $item
     * @param $barCodes
     * @return array|bool
     */
    public function getOrderItemsDefault($item, $barCodes)
    {
        if (!$item) {
            return false;
        }

        $this->source = $item;
        $data = $this->source->getData();

        $variableData = [];

        $groupNameVariables = __('Item Variables');
        $sourceVariables = $this->getVariablesOptionArray(true, $groupNameVariables, $data, 'order.item.');

        $groupNameBarcode = __('Source Barcode Variables');

        $sourceBarcodeVariables = [];
        if (!empty($barCodes)) {
            $sourceBarcodeVariables = $this->getBarCodeVariables(true, $groupNameBarcode, $data, 'ea_barcode_', '_order.item.', $barCodes);
        }


        foreach ($data as $dat => $val) {
            if (is_numeric($val)) {
                $variableData[$dat] = $val;
            } else {
                continue;
            }
        }

        $groupNameCurrency = __('Source Currency Variables');
        $sourceCurrencyVariables = $this->getDependCurrencyOptionArray(true, $groupNameCurrency, $data, 'ea_order.item.');

        $groupNameDepend = __('Source Depend Variables');
        $sourceDependVariables = $this->getDependCurrencyOptionArray(true, $groupNameDepend, $data, 'ea_order.item_if.');

        $standardVariables = [$sourceVariables, $sourceCurrencyVariables, $sourceDependVariables];

        return array_merge($standardVariables, $sourceBarcodeVariables);
    }

    /**
     * @param \Magento\Framework\DataObject $customer
     * @param $barCodes
     * @return array|bool
     */
    public function getCustomerDefault(\Magento\Framework\DataObject $customer, $barCodes)
    {
        if (!$customer) {
            return false;
        }

        $this->source = $customer;

        $data = $this->source->getData();

        $variableData = [];

        $groupNameVariables = __('Customer Variables');
        $sourceVariables = $this->getVariablesOptionArray(true, $groupNameVariables, $data, 'customer.');

        $groupNameBarcode = '__(Customer Barcode Variables)';
        $sourceBarcodeVariables = [];

        if (!empty($barCodes)) {
            $sourceBarcodeVariables = $this->getBarCodeVariables(true, $groupNameBarcode, $data, 'ea_barcode_', '_customer.', $barCodes);
        }

        foreach ($data as $dat => $val) {
            if (is_numeric($val)) {
                $variableData[$dat] = $val;
            } else {
                continue;
            }
        }

        $groupNameDepend = __('Customer Depend Variables');
        $sourceDependVariables = $this->getDependCurrencyOptionArray(true, $groupNameDepend, $data, 'customer_if.');

        $standardVariables = [$sourceVariables, $sourceDependVariables];

        return array_merge($standardVariables, $sourceBarcodeVariables);
    }

    /**
     * @param \Magento\Framework\DataObject $customer
     * @param $barCodes
     * @return array|bool
     */
    public function getOrderDefault(\Magento\Framework\DataObject $customer, $barCodes)
    {
        if (!$customer) {
            return false;
        }

        $this->source = $customer;

        $data = $this->source->getData();

        $variableData = [];

        $groupNameVariables = __('Order Variables');
        $sourceVariables = $this->getVariablesOptionArray(true, $groupNameVariables, $data, 'order.');

        $groupNameBarcode = __('Order Barcode Variables');
        $sourceBarcodeVariables = [];

        if (!empty($barCodes)) {
            $sourceBarcodeVariables = $this->getBarCodeVariables(true, $groupNameBarcode, $data, 'ea_barcode_', '_order.', $barCodes);
        }
        foreach ($data as $dat => $val) {
            if (is_numeric($val)) {
                $variableData[$dat] = $val;
            } else {
                continue;
            }
        }

        $groupNameCurrency = __('Order Currency Variables');
        $sourceCurrencyVariables = $this->getDependCurrencyOptionArray(true, $groupNameCurrency, $data, 'ea_order.');

        $groupNameDepend = __('Order Depend Variables');
        $sourceDependVariables = $this->getDependCurrencyOptionArray(true, $groupNameDepend, $data, 'order_if.');

        $standardVariables = [$sourceVariables, $sourceCurrencyVariables, $sourceDependVariables,];

        return array_merge($standardVariables, $sourceBarcodeVariables);
    }


    /**
     * Retrieve option array of variables
     *
     * @param boolean $withGroup if true wrap variable options in group
     * @param $variables , the passed variables for processing
     * @param $groupLabel , the label for the new variable group
     * @param $prefix , the prefix with dot to get the correct var name
     * @return array
     */
    public function getVariablesOptionArray($withGroup = false, $groupLabel, $variables, $prefix)
    {
        $optionArray = [];

        if ($variables) {
            foreach ($variables as $value => $label) {
                $optionArray[] = [
                    'value' => '{{' . 'var ' . $prefix . $value . '}}',
                    'label' => __('%1', $this->createNameFromValue($value)) . ' - ({{' . 'var ' . $prefix . $value . '}})'
                ];
                sort($optionArray);
            }
            if ($withGroup) {
                $optionArray = [
                    'label' => __($groupLabel),
                    'value' => $optionArray
                ];
            }
        }
        return $optionArray;
    }

    /**
     * @param $objectValue
     * @return string
     */
    private function createNameFromValue($objectValue)
    {
        $label = ucfirst(str_replace('_', ' ', $objectValue));
        return $label;
    }

    /**
     * @param bool $withGroup
     * @param $groupLabel
     * @param $variables
     * @param $prefix
     * @param $suffix
     * @param $barcodes
     * @return array
     */
    public function getBarCodeVariables($withGroup = false, $groupLabel, $variables, $prefix, $suffix, $barcodes)
    {

        $variablesToOptionArray = [];

        foreach ($barcodes as $code) {
            if ($variables) {
                foreach ($variables as $value => $label) {
                    $variablesToOptionArray[] = [
                        'value' => '{{' . 'var ' . $prefix . $code . $suffix . $value . '}}',
                        'label' => __('%1', $this->createNameFromValue($value)) . ' - ({{' . 'var ' . $prefix . $code . $suffix . $value . '}})'
                    ];
                    sort($variablesToOptionArray);
                }
            }

            if ($withGroup) {
                $optionArray[] = [
                    'label' => __($groupLabel) . ' ' . $code,
                    'value' => $variablesToOptionArray
                ];
            }

            $variablesToOptionArray = [];
        }

        return $optionArray;

    }

    /**
     * @param bool $withGroup
     * @param $groupLabel
     * @param $variables
     * @param $prefix
     * @return array
     */
    public function getDependCurrencyOptionArray($withGroup = false, $groupLabel, $variables, $prefix)
    {
        $optionArray = [];

        if ($variables) {
            foreach ($variables as $value => $label) {
                $optionArray[] = [
                    'value' => '{{' . 'var ' . $prefix . $value . '}}',
                    'label' => __('%1', $this->createNameFromValue($value)) . ' - ({{' . 'var ' . $prefix . $value . '}})'
                ];
                sort($optionArray);
            }
            if ($withGroup) {
                $optionArray = [
                    'label' => __($groupLabel),
                    'value' => $optionArray
                ];
            }
        }

        return $optionArray;
    }

}
