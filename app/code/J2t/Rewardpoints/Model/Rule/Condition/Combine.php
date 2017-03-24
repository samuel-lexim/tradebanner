<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Rule\Condition;


class Combine extends \Magento\Rule\Model\Condition\Combine //\Magento\SalesRule\Model\Rule\Condition\Combine
{
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \J2t\Rewardpoints\Model\Rule\Condition\Address $conditionAddress,
        array $data = []
    ) {
        $this->_eventManager = $eventManager;
        $this->_conditionAddress = $conditionAddress;
        parent::__construct($context, $data);
        $this->setType('J2t\Rewardpoints\Model\Rule\Condition\Combine');
    }
    
    
    public function getNewChildSelectOptions()
    {
        
        $conditions = parent::getNewChildSelectOptions();
        
        $conditions = array_merge_recursive($conditions, [
                [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Found',
                    'label' => __('Product attribute combination'),
                ],
                [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Subselect',
                    'label' => __('Products subselection')
                ],
                [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
                    'label' => __('Conditions combination')
                ]
            ]
        );
        
        
        $c_attributes = [
            ['value'=>'J2t\Rewardpoints\Model\Rule\Condition\CustomerAddress\Params|postcode', 'label'    =>  __('User post code')],
            ['value'=>'J2t\Rewardpoints\Model\Rule\Condition\CustomerAddress\Params|region_id', 'label'   =>  __('User region')],
            ['value'=>'J2t\Rewardpoints\Model\Rule\Condition\CustomerAddress\Params|country_id', 'label'  =>  __('User country')]
        ];
        
        $conditions = array_merge_recursive($conditions, [
            ['label'    => __('User location'), 'value'    => $c_attributes],
        ]);
        
        /*$c_attributes = array(
            array('value'=>'rewardpoints/rule_condition_customeraddress_params|postcode', 'label'=>Mage::helper('rewardpoints')->__('User post code')),
            array('value'=>'rewardpoints/rule_condition_customeraddress_params|region_id', 'label'=>Mage::helper('rewardpoints')->__('User region')),
            array('value'=>'rewardpoints/rule_condition_customeraddress_params|country_id', 'label'=>Mage::helper('rewardpoints')->__('User country'))
        );
        
        $conditions = array_merge_recursive($conditions, array(
            array('label'=>Mage::helper('rewardpoints')->__('User location'), 'value'=>$c_attributes),
        ));*/
        
        //$addressCondition = Mage::getModel('rewardpoints/rule_condition_address_address');
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $cart_attributes = array();
        foreach ($addressAttributes as $code => $label) {
            $cart_attributes[] = [
                'value' => 'J2t\Rewardpoints\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];;
        }

        $conditions = array_merge_recursive($conditions, array(
            array('label'   =>  __('Cart Attributes'), 'value'  =>  $cart_attributes),
        ));
        
        
        return $conditions;
        
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Magento\SalesRule\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Found',
                    'label' => __('Product attribute combination'),
                ],
                [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Subselect',
                    'label' => __('Products subselection')
                ],
                [
                    'value' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
                    'label' => __('Conditions combination')
                ],
                ['label' => __('Cart Attribute'), 'value' => $attributes]
            ]
        );

        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('salesrule_rule_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}

