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
use Magento\Framework\Api\SearchCriteriaBuilder;
use Eadesigndev\Pdfgenerator\Model\PdfgeneratorRepository as TemplateRepository;

class Standard extends Template
{

    /**
     * Currency constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Email\Model\Template\Config $_emailConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param TemplateRepository $templateRepository
     * @param DefaultVariables $_defaultVariablesHelper
     * @param SearchCriteriaBuilder $_criteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Email\Model\Template\Config $_emailConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        TemplateRepository $templateRepository,
        DefaultVariables $_defaultVariablesHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $_criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    )
    {
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

        $template = $this->_initTemplate();

        $id = $this->getRequest()->getParam('template_id');

        if (!$id) {
            return;
        }

        $templateModel = $this->templateRepository->getById($id);
        $templateType = $templateModel->getData('template_type');
        $type = \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPES[$templateType];

        /**if json error see https://github.com/magento/magento2/commit/02bc3fc42bf041919af6200f5dbba071ae3f2020 */

        try {
            $parts = $this->_emailConfig->parseTemplateIdParts('sales_email_' . $type . '_template');
            $templateId = $parts['templateId'];
            $theme = $parts['theme'];

            if ($theme) {
                $template->setForcedTheme($templateId, $theme);
            }
            $template->setForcedArea($templateId);

            $template->loadDefault($templateId);
            $template->setData('orig_template_code', $templateId);
            $template->setData('template_variables', \Zend_Json::encode($template->getVariablesOptionArray(true)));

            $templateBlock = $this->_view->getLayout()->createBlock('Magento\Email\Block\Adminhtml\Template\Edit');
            $template->setData('orig_template_currently_used_for', $templateBlock->getCurrentlyUsedForPaths(false));

            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($template->getData())
            );
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        $customVariables = $this->_objectManager->create('Magento\Variable\Model\Variable')
            ->getVariablesOptionArray(true);

        $storeContactVariables = $this->_objectManager->create(
            'Magento\Email\Model\Source\Variables'
        )->toOptionArray(
            true
        );
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $result = $resultJson->setData(
            [
                $storeContactVariables,
                $template->getVariablesOptionArray(true),
                $customVariables
            ]
        );

        return $this->addResponse($result);
    }


}
