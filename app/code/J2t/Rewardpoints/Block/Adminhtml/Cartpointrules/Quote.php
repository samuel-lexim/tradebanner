<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Cartpointrules;

/**
 * Catalog price rules
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Quote extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		//$this->_blockGroup = 'J2t_Rewardpoints';
        $this->_controller = 'adminhtml_cartpointrules_quote';
        $this->_headerText = __('Shopping Cart Price Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}
