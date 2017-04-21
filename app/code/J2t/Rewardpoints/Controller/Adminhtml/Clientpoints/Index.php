<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Adminhtml\Clientpoints;

class Index extends \Magento\Backend\App\Action
{
    protected $resultForwardFactory;
    protected $resultPageFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }
    
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
        $resultPage->setActiveMenu('J2t_Rewardpoints::system_rewardpoints_points');
        $resultPage->getConfig()->getTitle()->prepend(__('Client Points'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Rewardpoints'), __('Client Points'));
        //$resultPage->addBreadcrumb(__('Manage Customers'), __('Manage Customers'));

        //$this->_getSession()->unsCustomerData();

        return $resultPage;
    }
}
