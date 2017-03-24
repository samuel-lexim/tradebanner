<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace J2t\Rewardpoints\Block\Adminhtml\Grid\Column\Renderer;

/**
 * Adminhtml grid item renderer currency
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Order extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency
{
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $order_id = $row->getData($this->getColumn()->getIndex());
        if ($order_id > 0){
            $data = $order_id;
        } else {
            $data = __('- not relevant -');
        }
        
        return $data;
    }
}
