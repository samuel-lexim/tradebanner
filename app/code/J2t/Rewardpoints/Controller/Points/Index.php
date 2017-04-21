<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Points;

class Index extends \J2t\Rewardpoints\Controller\Points
{
    /**
     * Render my product reviews
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        if ($navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('rewardpoints/points');
        }
        //if ($block = $this->_view->getLayout()->getBlock('review_customer_list')) {
        if ($block = $this->_view->getLayout()->getBlock('point_customer_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getPage()->getConfig()->getTitle()->set(__('My Loyalty Points'));

        $this->_view->renderLayout();
    }
}
