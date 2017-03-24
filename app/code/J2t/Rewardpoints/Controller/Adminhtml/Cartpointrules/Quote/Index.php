<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Adminhtml\Cartpointrules\Quote;

class Index extends \J2t\Rewardpoints\Controller\Adminhtml\Cartpointrules\Quote
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Reward Points'), __('Reward Points'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Cart Point Rules'));
        $this->_view->renderLayout();
    }
}
