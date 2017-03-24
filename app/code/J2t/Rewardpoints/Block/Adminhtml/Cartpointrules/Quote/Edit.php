<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Cartpointrules\Quote;

/**
 * Shopping cart rule edit form block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        
        $this->_formScripts[] = "
            checkTypes = function(){
                if (jQuery('#rule_action_type').val() == '". \J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_DONTPROCESS."'){
                    jQuery('#rule_points').val('1');
                    jQuery('#rule_points').closest('.field').hide();
                } else if (jQuery('#rule_action_type').val() == '".\J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_DONTPROCESS_USAGE."'){
                    jQuery('#rule_points').val('1');
                    jQuery('#rule_points').closest('.field').hide();
                } else {
                    jQuery('#rule_points').closest('.field').show();
                }
            };
            
            jQuery(document).ready(function() {
            
                jQuery('#rule_action_type').change(function() {
                    checkTypes();
                });

                checkTypes();
            });
            
        ";
        
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        //$this->_controller = 'adminhtml_cartpointsrule';
        $this->_controller = 'adminhtml_cartpointrules_quote';
        
        $this->_blockGroup = 'J2t_Rewardpoints';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $rule = $this->_coreRegistry->registry('current_cart_point_rule');
        if ($rule->getRuleId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($rule->getName()));
        } else {
            return __('New Rule');
        }
    }

    /**
     * Retrieve products JSON
     *
     * @return string
     */
    public function getProductsJson()
    {
        return '{}';
    }
}
