<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 */

namespace Magebright\CustomerApprove\Controller\Adminhtml\Customer;

use Magento\Customer\Model\Session;
use Magento\Backend\App\Action\Context;
use Magebright\CustomerApprove\Model\Approve;
use Magento\Customer\Model\CustomerFactory;
use Magebright\CustomerApprove\Helper\Data as ApproveHelper;

class Reject extends \Magebright\CustomerApprove\Controller\Adminhtml\Customer
{
    /**
     * Reject customer registration.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id');
        if(true === $this->_processApprove($customerId, Approve::REJECTED)) {
            $this->messageManager->addSuccess(__('Customer was successfully rejected.'));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
