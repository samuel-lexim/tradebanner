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

use Magento\Framework\App\Action\Action;
use Eadesigndev\Opicmsppdfgenerator\Helper\Variable\DefaultVariables;

abstract class Template extends Action
{

    CONST INVOICE_TMEPLTE_ID = 'sales_email_invoice_template';
    CONST ORDER_TMEPLTE_ID = 'sales_email_order_template';
    CONST SHIPMENT_TMEPLTE_ID = 'sales_email_shipment_template';
    CONST CREDITMEMO_TMEPLTE_ID = 'sales_email_creditmemo_template';

    CONST ADMIN_RESOURCE_VIEW = 'Eadesigndev_Pdfgenerator::templates';

    /**
     * @var TemplateRepository
     */
    protected $templateRepository;

    /**
     * @var DefaultVariables
     */
    protected $_defaultVariablesHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Email\Model\Template\Config
     */
    protected $_emailConfig;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_criteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Email\Model\Template\Config $_emailConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        DefaultVariables $_defaultVariablesHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $_criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    )
    {
        $this->_criteriaBuilder = $_criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->_emailConfig = $_emailConfig;
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_defaultVariablesHelper = $_defaultVariablesHelper;
    }


    /**
     * Load email template from request
     *
     * @return \Magento\Email\Model\BackendTemplate $model
     */
    protected function _initTemplate()
    {

        $model = $this->_objectManager->create('Magento\Email\Model\BackendTemplate');

        if (!$this->_coreRegistry->registry('email_template')) {
            $this->_coreRegistry->register('email_template', $model);
        }
        if (!$this->_coreRegistry->registry('current_email_template')) {
            $this->_coreRegistry->register('current_email_template', $model);
        }
        return $model;
    }

    /**
     * @param $templateTypeName
     * @return mixed
     */
    protected function collection($templateTypeName)
    {
        $this->_criteriaBuilder->addFilters(
            [$this->filterBuilder
                ->setField('entity_id')
                ->setValue($this->getRequest()->getParam('variables_entity_id'))
                ->setConditionType('eq')
                ->create()]
        );
        $searchCriteria = $this->_criteriaBuilder->create();

        $collection = $this->_objectManager->create(
            'Magento\Sales\Api\\' .
            ucfirst($templateTypeName) .
            'RepositoryInterface')->getList($searchCriteria);
        return $collection;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(\Eadesigndev\Pdfgenerator\Controller\Adminhtml\Templates::ADMIN_RESOURCE_VIEW);
    }

    protected function addResponse($result)
    {
        if(!empty($result)){
            return $result;
        }

        $template = $this->_initTemplate();

        $resultJson = $this->resultJsonFactory->create();

        $optionArray[] = ['value' => '{{' . '' . '}}', 'label' => __('%1', '')];

        $optionArray = ['label' => __('There are no variable available, please check the source value.'), 'value' => $optionArray];

        $result = $resultJson->setData(
            [
                $optionArray
            ]
        );

        return $result;

    }
}
