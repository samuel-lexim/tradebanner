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

class Orderitem extends Template
{

    private $customData;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Email\Model\Template\Config $_emailConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        TemplateRepository $templateRepository,
        DefaultVariables $_defaultVariablesHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $_criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Custom\Items\Items $customData
    )
    {
        $this->templateRepository = $templateRepository;
        parent::__construct($context, $coreRegistry, $_emailConfig, $resultJsonFactory, $_defaultVariablesHelper, $_criteriaBuilder, $filterBuilder);
        $this->_coreRegistry = $coreRegistry;
        $this->customData = $customData;
    }

    /**
     * WYSIWYG Plugin Action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        $this->_initTemplate();

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

        $source = $collection->getLastItem();

        $items = $source->getItems();

        foreach ($items as $item) {
            $dataItem = $item;
        }

        if (!$dataItem instanceof \Magento\Sales\Model\Order\Item) {
            $dataItem = $dataItem->getOrderItem();
        }

        $lastItem = $this->customData->entity($dataItem)->processAndReadVariables();

        $model = $this->templateRepository->getById($id);
        $barCodes = [];
        if (!empty($model->getData('barcode_types'))) {
            $barCodes = explode(',', $model->getData('barcode_types'));
        }

        $invoiceVariables = $this->_defaultVariablesHelper->getOrderItemsDefault($lastItem, $barCodes);

        $result = $resultJson->setData($invoiceVariables);

        return $this->addResponse($result);
    }

}
