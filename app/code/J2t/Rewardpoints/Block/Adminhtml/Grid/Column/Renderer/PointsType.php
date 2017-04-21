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
class PointsType extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency
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
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $status_field = $objectManager->get('J2t\Rewardpoints\Helper\Data')->getStatusField();
        $model = $objectManager->get('J2t\Rewardpoints\Model\Point');
        $points_type = $model->getPointsDefaultTypeToArray();
        $points_type[\J2t\Rewardpoints\Model\Point::TYPE_POINTS_ADMIN] = __('Store input %1', ($row->getRewardpointsDescription()) ? ' - '.$row->getRewardpointsDescription() : '');
        
        if (\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REQUIRED == $order_id){
            $current_model = $model->load($row->getRewardpointsAccountId());
            if ($current_model->getQuoteId()){
                //$order_model = Mage::getModel('sales/order')->loadByAttribute('quote_id', $current_model->getQuoteId());
                $order_model = $objectManager->get('Magento\Sales\Model\Order')->loadByAttribute('quote_id', $current_model->getQuoteId());
                if ($order_model->getIncrementId()){
                    $points_type[\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REQUIRED] = __('Required point usage for order #%1 (%2)', $order_model->getIncrementId(), $order_model->getData($status_field));
                }
            }
        }

        if ( ($order_id > 0) || ($order_id != "" && !is_numeric($order_id)) ){
            $order = $objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($order_id);
            return __('Points related to order #%1 (%2)', $order_id, $order->getData($status_field));
        } elseif (isset($points_type[$order_id])) {
            return $points_type[$order_id];
        } else {
            return null;
        }
        
    }
}
