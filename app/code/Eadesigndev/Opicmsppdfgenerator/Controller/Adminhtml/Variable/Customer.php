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

namespace Eadesigndev\Opicmsppdfgenerator\Controller\Adminhtml\Variable;

use Eadesigndev\Opicmsppdfgenerator\Helper\Variable\DefaultVariables;
use Eadesigndev\Pdfgenerator\Model\PdfgeneratorRepository as TemplateRepository;
use Magento\Framework\DataObject;


class Customer extends Template
{

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    private $_extensibleDataObjectConverter;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Email\Model\Template\Config $_emailConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        TemplateRepository $templateRepository,
        DefaultVariables $_defaultVariablesHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $_criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    )
    {
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->templateRepository = $templateRepository;
        parent::__construct($context, $coreRegistry, $_emailConfig, $resultJsonFactory, $_defaultVariablesHelper, $_criteriaBuilder, $filterBuilder);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * WYSIWYG Plugin Action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        $id = $this->getRequest()->getParam('template_id');

        if (!$id) {
            return;
        }

        $templateModel = $this->templateRepository->getById($id);
        $templateType = $templateModel->getData('template_type');

        $templateTypeName = \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPES[$templateType];

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $collection = $this->collection($templateTypeName);
        if (!count($collection)) {
            return;
        }

        $id = $this->getRequest()->getParam('template_id');

        if (!$id) {
            return;
        }

        $source = $collection->getLastItem();

        if ($source instanceof \Magento\Sales\Model\Order) {
            $order = $source;
        } else {
            $order = $source->getOrder();
        }

        if ($customerId = $order->getCustomerId()) {

            $customer = $this->_objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface')->getById($customerId);

            $customerData = $this->_extensibleDataObjectConverter->toFlatArray(
                $customer,
                [],
                '\Magento\Customer\Api\Data\CustomerInterface'
            );
        }

        $model = $this->templateRepository->getById($id);
        $barCodes = [];
        if (!empty($model->getData('barcode_types'))) {
            $barCodes = explode(',', $model->getData('barcode_types'));
        }

        $pseudoCustomer = new DataObject($customerData);

        $invoiceVariables = $this->_defaultVariablesHelper->getCustomerDefault($pseudoCustomer, $barCodes);

        $result = $resultJson->setData($invoiceVariables);

        return $this->addResponse($result);
    }

}
