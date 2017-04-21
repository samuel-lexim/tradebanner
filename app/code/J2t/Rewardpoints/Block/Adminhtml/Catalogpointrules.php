<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml;

/**
 * Adminhtml sales orders block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Catalogpointrules extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_catalogpointsrule';
        $this->_blockGroup = 'J2t_Rewardpoints';
        $this->_headerText = __('Catalog Point Rules');
        $this->_addButtonLabel = __('Create New Rule');
        parent::_construct();
        if (!$this->_authorization->isAllowed('J2t_Rewardpoints::system_rewardpoints_catalog_rule')) {
            $this->buttonList->remove('add');
        }
    }

    /**
     * Retrieve url for order creation
     *
     * @return string
     */
    public function getCreateUrl()
    {
        //return $this->getUrl('sales/order_create/start');
        return $this->getUrl('rewardpoints_admin/catalogpointrules_create/start');
    }
}
