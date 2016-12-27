<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 
 */

namespace Magebright\CustomerApprove\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class MassReject extends AbstractMassAction
{
    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $customersRejected = 0;
        foreach ($collection->getAllIds() as $customerId) {
            $this->_processApprove($customerId, \Magebright\CustomerApprove\Model\Approve::REJECTED);
            $customersRejected++;
        }

        if ($customersRejected) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were rejected.', $customersRejected));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
