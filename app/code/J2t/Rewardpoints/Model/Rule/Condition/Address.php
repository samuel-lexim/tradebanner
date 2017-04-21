<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Rule\Condition;


class Address extends \Magento\SalesRule\Model\Rule\Condition\Address
{
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods,
        array $data = []
    ) {
        parent::__construct($context, $directoryCountry, $directoryAllregion, $shippingAllmethods, $paymentAllmethods, $data);
        /*$this->_directoryCountry = $directoryCountry;
        $this->_directoryAllregion = $directoryAllregion;
        $this->_shippingAllmethods = $shippingAllmethods;
        $this->_paymentAllmethods = $paymentAllmethods;*/
    }
    
    public function loadAttributeOptions()
    {
        
        $temp = parent::loadAttributeOptions();

        $attributes = $temp->getAttributeOption();
        $attributes = ['base_subtotal_incl_tax' => __('Subtotal (Incl. Tax)')] + $attributes;

        $this->setAttributeOption($attributes);

        return $this;
        
        /*$attributes = [
            'base_subtotal' => __('Subtotal'),
            'total_qty' => __('Total Items Quantity'),
            'weight' => __('Total Weight'),
            'payment_method' => __('Payment Method'),
            'shipping_method' => __('Shipping Method'),
            'postcode' => __('Shipping Postcode'),
            'region' => __('Shipping Region'),
            'region_id' => __('Shipping State/Province'),
            'country_id' => __('Shipping Country'),
        ];

        $this->setAttributeOption($attributes);

        return $this;*/
    }
    
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal_incl_tax':
                return 'numeric';
        }
        return parent::getInputType();
    }
    
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if (!is_object($model->getQuote())){
            $model->setQuote($model);
        }
        $address = $model;
        /*if (!$address instanceof \Magento\Quote\Model\Quote\Address) {
            if ($model->getQuote()->isVirtual()) {
                $address = $model->getQuote()->getBillingAddress();
            } else {
                $address = $model->getQuote()->getShippingAddress();
            }
        }

        if ('payment_method' == $this->getAttribute() && !$address->hasPaymentMethod()) {
            $address->setPaymentMethod($model->getQuote()->getPayment()->getMethod());
        }*/
		
		//$address->setBaseSubtotalIncTax($address->getOrigData('base_subtotal') + $address->getOrigData('base_tax_amount'));
		
        return parent::validate($address);
    }
    
    
}
