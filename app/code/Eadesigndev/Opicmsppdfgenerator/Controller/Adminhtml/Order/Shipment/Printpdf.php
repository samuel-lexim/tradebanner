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

namespace Eadesigndev\Opicmsppdfgenerator\Controller\Adminhtml\Order\Shipment;

use Eadesigndev\Pdfgenerator\Controller\Adminhtml\Order\Abstractpdf;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;

class Printpdf extends Abstractpdf
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $_dateTime;

    /**
     * @var \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output
     */
    private $helper;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $_fileFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * Printpdf constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output $helper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $_dateTime
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $_dateTime,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    )
    {
        $this->_fileFactory = $fileFactory;
        $this->helper = $helper;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
        parent::__construct($context, $coreRegistry, $emailConfig, $resultJsonFactory);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_dateTime = $_dateTime;
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam('template_id');
        if (!$templateId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $templateModel = $this->_objectManager->create('Eadesigndev\Pdfgenerator\Api\TemplatesRepositoryInterface')->getById($templateId);
        if (!$templateModel->getData('template_type')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $shipmentId = $this->getRequest()->getParam('shipment_id');
        if (!$shipmentId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $shipment = $this->_objectManager->create('Magento\Sales\Api\ShipmentRepositoryInterface')->get($shipmentId);
        if (!$shipment) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $helper = $this->helper;

        $helper->setSource($shipment);
        $helper->setTemplate($templateModel);

        if ($customerId = $shipment->getOrder()->getCustomerId()) {
            $pseudoCustomer = $this->customer($customerId);
            $helper->setCustomer($pseudoCustomer);
        }

        $pdfFileData = $helper->template2Pdf();
        $output = $helper->PDFmerger();

        $date = $this->_dateTime->date('Y-m-d_H-i-s');

        $fileName = $pdfFileData['filename'] . $date . '.pdf';

        return $this->_fileFactory->create(
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
