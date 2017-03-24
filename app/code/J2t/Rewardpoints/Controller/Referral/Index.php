<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Referral;

class Index extends \J2t\Rewardpoints\Controller\Referral
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

        if ($navigationBlock = $this->_view->getLayout()->getBlock('referral_customer_list')) {
            $navigationBlock->setActive('rewardpoints/referral');
        }
        if ($block = $this->_view->getLayout()->getBlock('referral_customer_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getPage()->getConfig()->getTitle()->set(__('My Referral Program'));

        $this->_view->renderLayout();
    }
    
}
