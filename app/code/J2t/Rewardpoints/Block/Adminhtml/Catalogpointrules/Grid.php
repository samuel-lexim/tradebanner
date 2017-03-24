<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Catalogpointrules;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CatalogRule';
        //$this->_controller = 'adminhtml_promo_catalog';
        $this->_controller = 'adminhtml_catalogpointrules_catalog';
        
        $this->_headerText = __('Catalog Point Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();

        //$this->buttonList->add(
        //    'apply_rules',
        //    [
        //        'label' => __('Apply Rules'),
        //        'onclick' => "location.href='" . $this->getUrl('catalog_rule/*/applyRules') . "'",
        //        'class' => 'apply'
        //    ]
        //);
    }
}
