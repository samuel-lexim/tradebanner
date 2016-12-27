<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove

 */

namespace Magebright\CustomerApprove\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magebright\CustomerApprove\Model\Approve;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magebright\CustomerApprove\Helper\Data as ApproveHelper;
use Magento\Customer\Api\Data\CustomerExtensionFactory;

abstract class Customer extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var Approve
     */
    protected $approve;

    /**
     * @var \Magebright\CustomerApprove\Helper\Data
     */
    protected $helper;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerExtensionFactory
     */
    protected $customerExtensionFactory;

    /**
     * Constructor.
     * 
     * @param Context                     $context
     * @param Approve                    $approve
     * @param ApproveHelper              $helper
     * @param CustomerFactory             $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerExtensionFactory    $customerExtensionFactory
     */
    public function __construct(
        Context $context,
        Approve $approve,
        ApproveHelper $helper,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerExtensionFactory $customerExtensionFactory
    ) {
        $this->approve = $approve;
        $this->helper = $helper;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->customerExtensionFactory = $customerExtensionFactory;

        parent::__construct($context);
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebright_CustomerApprove::manage');
    }

    /**
     * Set customer approve status.
     *
     * @param int $id
     * @param int $status
     *
     * @return void
     */
    protected function _processApprove($id, $status)
    {
        $customer = $this->customerRepository->getById($id);
        try {
            $this->helper->saveApproveStatus($customer, $status);

            $storeId = $customer->getStoreId();
            if(false === $this->helper->canNotifyCustomer($storeId)) {
                return true;
            }

            if($status == Approve::REJECTED) {
                $templateId = $this->helper->getRejectedEmailTemplateId($storeId);
            } else {
                $templateId = $this->helper->getApprovedEmailTemplateId($storeId);
            }

            $customerModel = $this->customerFactory->create()->load($customer->getId());
            $templateData = [
                'customer' => $customerModel,
                'store' => $this->helper->getStore($storeId),
            ];

            $this->helper->sendEmailTemplate(
                $customerModel->getName(),
                $customer->getEmail(),
                $templateId,
                $this->helper->getSender(null, $storeId),
                $templateData,
                $storeId
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return false;
        }

        return true;
    }
}
