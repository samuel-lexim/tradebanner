<?php

namespace FME\Pricecalculator\Observer;



class UpdatePriceCart implements \Magento\Framework\Event\ObserverInterface {

    protected $_request;
    protected $_pcHelper;
    protected $_objectManager;
    
    
    public function __construct(\Magento\Framework\App\RequestInterface $request,
            \FME\Pricecalculator\Helper\Data $_pcHelper,
            \Magento\Framework\ObjectManagerInterface $objectManager) {

        $this->_request = $request;
        $this->_pcHelper = $_pcHelper;
        $this->_objectManager = $objectManager;
    }

    
    public function execute(\Magento\Framework\Event\Observer $observer) {

        $quoteItem = $observer->getEvent()->getQuoteItem();        
        $_product = $observer->getProduct();                
        $customOptions = $_product->getOptions();
        $totalOp = 0;
        
        if($customOptions && $_product->getPricingLimit() != ''){
        
            $fieldOptions = $this->_pcHelper->getFieldOptions($_product);
            
            if($fieldOptions){
                foreach ($customOptions as $option):
                    if (isset($fieldOptions[$option->getTitle()])){
                        $totalOp++;
                    }
                endforeach;
            }
        }
        
        if($totalOp == 0){
            return;
        }
        
        
        $params = $this->_request->getParams();
        
        $posted_options = $params['options'];
        
        $final_price = $quoteItem->getProduct()->getFinalPrice(); //base-price + selected options
        $price = $this->_calculatePrice($_product, $posted_options);
        
        $updatePrice = $price + $final_price;
        
        
        $item = ( $quoteItem->getParentItem() ? $quoteItem->getParentItem() : $quoteItem );
        $item->setCustomPrice($updatePrice);
        $item->setOriginalCustomPrice($updatePrice);
        $item->getProduct()->setIsSuperMode(true);
                     
        
    }
    
    
    
    protected function _calculatePrice($_product, $posted_options){
        
        $unitPrice = $_product->getPriceUnitArea();
        $area = $this->_calculateArea($_product, $posted_options);
        
        $discount = $this->_calculateDiscount($area, $unitPrice, $_product);
        
        $price = ($unitPrice * $area) - $discount;
        
        return $price;
    }
    
    
    protected function _calculateArea($_product, $posted_options){
        
        $customOptions = $_product->getOptions();
        $fieldOptions = $this->_pcHelper->getFieldOptions($_product);
        $area = 1; 
         
        foreach ($customOptions as $option):
            if (isset($fieldOptions[$option->getTitle()])){
                
                $posted_val = $posted_options[$option->getId()];                
                $area = $area * (float) $posted_val;
            }
        endforeach;
        
        $inputUnit = $this->_pcHelper->getInputUnitLabel($_product);
        $outputUnit = $this->_pcHelper->getOutputUnitLabel($_product);
        $unitCoversion = $this->_pcHelper->unitConversion($inputUnit, $outputUnit);
        
        $area = $area * (float) $unitCoversion;
        
        return $area;
    }
    
    
    protected function _calculateDiscount($area, $unitPrice, $_product){
            
            $rules = $this->_pcHelper->getProductPricingRule($_product);
            $discount = 0;
            
            if($area < $rules['size']['min_limit']){
                $discount =  0;
            }else
            if($area >= $rules['size']['min_limit'] && $area < $rules['size']['max_limit']){
                if($rules['type'] == 'percent'){
                    $discount =  ( ($area*$unitPrice) * ($rules['discount']['min_limit'] / 100));
                }else{
                    $discount =  $rules['discount']['min_limit'];
                }                
            }else
            if($area >= $rules['size']['max_limit']){
                if($rules['type'] == 'percent'){
                    $discount =  ($area*$unitPrice) * ($rules['discount']['max_limit'] / 100);
                }else{
                    $discount =  ($rules['discount']['max_limit']);
                }
            }
            
            
            return $discount;
    }
    
    
}
