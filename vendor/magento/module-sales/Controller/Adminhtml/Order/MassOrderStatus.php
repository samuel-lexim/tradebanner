<?php
/**
 * @author Samuel Kong
 * @company Lexim IT
 * Change status order in order grid page
 */

namespace Magento\Sales\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order\ShipmentFactory;

use Magebright\CustomerApprove\Helper\Data;
use \Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\DataObject;

class MassOrderStatus extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    protected $_shipmentFactory;
    protected $_invoiceService;
    protected $invoiceCollectionFactory;
    protected $transactionFactory;

    protected $helper;
    protected $customerRepositoryInterface;

    /**
     * MassOrderStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param InvoiceService $invoiceService
     * @param ShipmentFactory $shipmentFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        InvoiceService $invoiceService,
        ShipmentFactory $shipmentFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        Data $helper,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;

        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_invoiceService = $invoiceService;
        $this->_shipmentFactory = $shipmentFactory;
        $this->transactionFactory = $transactionFactory;

        $this->helper = $helper;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * Cancel selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $status = $this->getRequest()->getParam('status');

        $countExecution = 0;
        foreach ($collection->getItems() as $order) {
            $order->setStatus($status);

            //START Handle Complete
            if ($status == 'complete') {
                $transactionSave = $this->transactionFactory->create();
                if ($order->canInvoice()) {
                    $invoice = $this->_invoiceService->prepareInvoice($order);
                    $invoice->register();
                    $invoice->getOrder()->setIsInProcess(true);
                    $transactionSave->addObject($order)->addObject($invoice);
                    if ($order->canShip()) {
                        $shipment = $this->_shipmentFactory->create($invoice->getOrder());
                        $shipment->register();

                        if ($shipment) {
                            $transactionSave->addObject($shipment);
                        }
                    }
                }
                $transactionSave->save();
            }
            //END Handle Complete

            $order->save();
            $countExecution++;

            // Send email
            $storeId = $order->getStoreId();
            $templateId = 'change_order_status_email_template';
            $customerId = $order->getCustomerId();
            $customerModel = $this->customerRepositoryInterface->getById($customerId);

            // Get Order object
            $orderData = [
                'status' => $status,
                'id' => $order->getIncrementId(),
                'customer_name' => $order->getCustomerName(),
                'customer_email' => $order->getCustomerEmail()
            ];
            $orderObj = new DataObject($orderData);

            $templateData = [
                'customer' => $customerModel,
                'store' => $this->helper->getStore($storeId),
                'order' => $orderObj
            ];

//            $this->helper->sendEmailTemplate(
//                $order->getCustomerName(),
//                $order->getCustomerEmail(),
//                $templateId,
//                $this->helper->getSender(null, $storeId),
//                $templateData,
//                $storeId
//            );
            // # Send email
        }

        $countDefeat = $collection->count() - $countExecution;
        if ($countDefeat && $countExecution) {
            $this->messageManager->addError(__('%1 order(s) cannot be changed status.', $countDefeat));
        } elseif ($countDefeat) {
            $this->messageManager->addError(__('No order statuses have been changed.'));
        }
        if ($countExecution) {
            $this->messageManager->addSuccess(__('%1 order(s) have been updated status.', $countExecution));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

}
