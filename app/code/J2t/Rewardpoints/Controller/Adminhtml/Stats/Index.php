<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Adminhtml\Stats;

class Index extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customers list action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }
        $resultPage = $this->resultPageFactory->create();
        
        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu('J2t_Rewardpoints::system_rewardpoints_stats');
        $resultPage->getConfig()->getTitle()->prepend(__('Statistics'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Rewardpoints'), __('Statistics'));
        //$resultPage->addBreadcrumb(__('Manage Customers'), __('Manage Customers'));

        //$this->_getSession()->unsCustomerData();

        return $resultPage;
    }
}
