<?php
namespace Magebright\CustomerApprove\Controller\Adminhtml\Customer;

use Magento\Customer\Model\Session;
use Magento\Backend\App\Action\Context;
use Magebright\CustomerApprove\Model\Approve;
use Magento\Customer\Model\CustomerFactory;
use Magebright\CustomerApprove\Helper\Data as ApproveHelper;

class Approved extends \Magebright\CustomerApprove\Controller\Adminhtml\Customer
{
    
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id');
        if(true === $this->_processApprove($customerId, Approve::APPROVED)) {
            $this->messageManager->addSuccess(__('Customer was successfully approved.'));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
