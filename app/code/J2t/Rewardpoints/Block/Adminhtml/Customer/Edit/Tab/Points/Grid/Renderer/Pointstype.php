<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Adminhtml\Customer\Edit\Tab\Points\Grid\Renderer;

/**
 * Adminhtml newsletter queue grid block status item renderer
 */
class Pointstype extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    protected $_pointModel = null;
    protected $_pointData = null;
    protected $_customerModel = null;
    protected $_orderModel = null;

    public function __construct(
    \Magento\Backend\Block\Context $context, \J2t\Rewardpoints\Model\Point $point, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Customer\Model\Customer $customer, \Magento\Sales\Model\Order $order, array $data = []
    ) {
        $this->_pointModel = $point;
        $this->_customerModel = $customer;
        $this->_orderModel = $order;
        $this->_pointData = $pointHelper;
        parent::__construct($context, $data);
    }

    /**
     * Constructor for Grid Renderer Status
     *
     * @return void
     */
    /* protected function _construct()
      {
      self::$_statuses = [
      \Magento\Newsletter\Model\Queue::STATUS_SENT => __('Sent'),
      \Magento\Newsletter\Model\Queue::STATUS_CANCEL => __('Cancel'),
      \Magento\Newsletter\Model\Queue::STATUS_NEVER => __('Not Sent'),
      \Magento\Newsletter\Model\Queue::STATUS_SENDING => __('Sending'),
      \Magento\Newsletter\Model\Queue::STATUS_PAUSE => __('Paused'),
      ];
      parent::_construct();
      } */

    /**
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $statusField = $this->_pointData->getStatusField();
        $orderId = $row->getData($this->getColumn()->getIndex());
        $model = $this->_pointModel;
        $pointsType = $model->getPointsDefaultTypeToArray();
        $pointsType[\J2t\Rewardpoints\Model\Point::TYPE_POINTS_ADMIN] = __('Store input %1', ($row->getRewardpointsDescription()) ? ' - ' . $row->getRewardpointsDescription() : '');

        if ($orderId == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_REFERRAL_REGISTRATION) {
            $currentModel = $model->load($row->getRewardpointsAccountId());
            $model = $this->_customerModel->load($currentModel->getRewardpointsLinker());
            if ($model->getName()) {
                return __('Referral registration points (%1)', $model->getName());
            }
        }

        if (\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REQUIRED == $orderId) {
            $currentModel = $model->load($row->getRewardpointsAccountId());
            if ($currentModel->getQuoteId()) {
                $orderModel = $this->_orderModel->loadByAttribute('quote_id', $currentModel->getQuoteId());
                if ($orderModel->getIncrementId()) {
                    $pointsType[\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REQUIRED] = __('Required point usage for order #%1 (%2)', $orderModel->getIncrementId(), __($orderModel->getData($statusField)));
                }
            }
        }

        if (($orderId > 0) || ($orderId != "" && !is_numeric($orderId))) {
            $order = $this->_orderModel->loadByIncrementId($orderId);
            return __('Points related to order #%1 (%2)', $orderId, __($order->getData($statusField)));
        } elseif (isset($pointsType[$orderId])) {
            return $pointsType[$orderId];
        }
        return null;
    }

}
