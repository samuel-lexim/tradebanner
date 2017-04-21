<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Rule\Condition\CustomerAddress;


class Params extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /*public function __construct()
    {
        parent::__construct();
        $this->setType('rewardpoints/rule_condition_customeraddress_params')
            ->setValue(null);
    }*/
    
    protected $_yesno = null;
    protected $_country = null;
    protected $_region = null;
    
    protected $customerRepository;
    protected $_backendData;
    
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        //adminhtml/system_config_source_yesno
        parent::__construct($context, $data);
        $this->_yesno = $yesno;
        $this->_country = $directoryCountry;
        $this->_region = $directoryAllregion;
        $this->customerRepository = $customerRepository;
        $this->_backendData = $backendData;
        
        /*$this->_directoryCountry = $directoryCountry;
        $this->_directoryAllregion = $directoryAllregion;
        $this->_shippingAllmethods = $shippingAllmethods;
        $this->_paymentAllmethods = $paymentAllmethods;*/
        //$this->setType('rewardpoints/rule_condition_customeraddress_params')->setValue(null);
    }
    
    public function loadAttributeOptions()
    {
        $attributes = [
            'postcode' => __('Zip/Postal Code'),
            'region_id' => __('Region'),
            'country_id' => __('Country'),
        ];
        $this->setAttributeOption($attributes);
        return $this;

    }


    public function loadOperatorOptions()
    {
        $this->setOperatorOption([
            '=='  => __('is'),
            '!='  => __('is not'),
            '>='  => __('equals or greater than'),
            '<='  => __('equals or less than'),
            '>'   => __('greater than'),
            '<'   => __('less than'),
        ]);
        return $this;
    }
    
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids':
                return true;
        }

        return false;
    }
    
    public function getValueElement()
    {
        $element = parent::getValueElement();

        return $element;
    }
    
    
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                }
                break;
            default:
                break;
        }
        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }
    
    public function asHtml()
    {
        if ($this->getAttribute()=='sku')
        {
            $html = $this->getTypeElement()->getHtml().
                    __("%s %s",
                       $this->getAttributeElement()->getHtml(),
                       $this->getValueElement()->getHtml()
            );
            if ($this->getId()!='1') {
                    $html.= $this->getRemoveLinkHtml();
            }
            return $html;
        }

        return parent::asHtml();
    }
    
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal': case 'weight': case 'total_qty':
                return 'numeric';

            case 'shipping_method': case 'payment_method': case 'country_id': case 'region_id':
                return 'select';
        }
        return 'string';
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }
    
    
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }



        return 'text';
    }
    
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {

            $options = array();

            if ($options == array()){
                switch ($this->getAttribute()) {
                    case 'confirmation':
                        //$options = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
                        $options = $this->_yesno->toOptionArray();
                        break;
                    case 'country_id':
                        /*$options = Mage::getModel('adminhtml/system_config_source_country')
                            ->toOptionArray();*/
                        $options = $this->_country->toOptionArray();
                        break;

                    case 'region_id':
                        /*$options = Mage::getModel('adminhtml/system_config_source_allregion')
                            ->toOptionArray();*/
                        $options = $this->_region->toOptionArray();
                        break;
                    default:
                        $options = array();
                }
            }

            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }
    
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        
        //$customerId = $model->getQuote()->getCustomerId();
		$customerId = $model->getCustomerId();
        if ($customerId){
            $customer = $this->customerRepository->getById($customerId);
            if ($address = $model->getPrimaryBillingAddress()){
                return parent::validate($address);
            }
        }

        return false;
    }
    
}

