<?php
/**
 * EaDesgin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eadesign.ro so we can send you a copy immediately.
 *
 * @category    eadesigndev_pdfgenerator
 * @copyright   Copyright (c) 2008-2016 EaDesign by Eco Active S.R.L.
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace Eadesigndev\Opicmsppdfgenerator\Controller\Adminhtml\Order\Massaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoty;

class Printpdf extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;


    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output
     */
    private $helper;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * Printpdf constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output $helper,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->fileFactory = $fileFactory;
        $this->date = $date;
        $this->helper = $helper;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
        parent::__construct($context, $filter);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_order');
    }

    /**
     * Print selected orders
     *
     * @param AbstractCollection $collection
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {

        $templateId = $this->getRequest()->getParam('template_id');

        if (!$templateId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $helper = $this->helper;

        foreach ($collection as $order) {

            $templateModel = $this->_objectManager->create('Eadesigndev\Pdfgenerator\Api\TemplatesRepositoryInterface')->getById($templateId);
            if (!$templateModel) {
                return $this->resultForwardFactory->create()->forward('noroute');
            }

            $helper->setSource($order);
            $helper->setTemplate($templateModel);

            if ($customerId = $order->getCustomerId()) {
                $pseudoCustomer = $this->customer($customerId);
                $helper->setCustomer($pseudoCustomer);
            }

            $helper->template2Pdf();
        }

        $output = $helper->PDFmerger($templateModel);

        $date = $this->date->date('Y-m-d_H-i-s');

        $fileName = 'mass_print_pdf_order' . $date . '.pdf';

        return $this->fileFactory->create(
            $fileName,
            $output,
            DirectoryList::MEDIA_ORDER_PDF,
            'application/pdf'
        );

    }

    /**
     * @param $customerId
     * @return DataObject
     */
    private function customer($customerId)
    {
        $customer = $this->_objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface')->getById($customerId);

        $customerData = $this->_extensibleDataObjectConverter->toFlatArray(
            $customer,
            [],
            '\Magento\Customer\Api\Data\CustomerInterface'
        );

        $pseudoCustomer = new DataObject($customerData);
        return $pseudoCustomer;
    }

}
