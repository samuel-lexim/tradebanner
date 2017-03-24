<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Cartpointrules\Quote\Edit;

/**
 * description
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardpoints_cart_point_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Reward Points Cart Point Rule'));
    }
}
