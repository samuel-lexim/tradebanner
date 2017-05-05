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

namespace Eadesigndev\Opicmsppdfgenerator\Controller\Adminhtml\Templates;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Eadesigndev\Pdfgenerator\Model\Source\TemplateActive;
use Eadesigndev\Pdfgenerator\Model\PdfgeneratorRepository as TemplateRepository;
use Eadesigndev\Pdfgenerator\Model\PdfgeneratorFactory;
use Eadesigndev\Pdfgenerator\Controller\Adminhtml\Templates\PdfDataProcessor;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Eadesign_Pdfgenerator::save';

    /**
     * @var PdfDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var TemplateRepository
     */
    protected $templateRepository;

    /**
     * @var PdfgeneratorFactory
     */
    protected $pdfgeneratorFactory;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param PdfDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param TemplateRepository $templateRepository
     * @param PdfgeneratorFactory $pdfgeneratorFactory
     */
    public function __construct(
        Action\Context $context,
        PdfDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor,
        TemplateRepository $templateRepository,
        PdfgeneratorFactory $pdfgeneratorFactory
    )
    {
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->templateRepository = $templateRepository;
        $this->pdfgeneratorFactory = $pdfgeneratorFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->dataProcessor->validateRequireEntry($data);
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = TemplateActive::STATUS_ENABLED;
            }
            if (empty($data['template_id'])) {
                $data['template_id'] = null;
            }

            /** @var \Eadesigndev\Pdfgenerator\Model\Pdfgenerator $model */

            $id = $this->getRequest()->getParam('template_id');
            if ($id) {
                $model = $this->templateRepository->getById($id);
            } else {
                unset($data['template_id']);
                $model = $this->pdfgeneratorFactory->create();
            }

            $model->setData($data);

            $model->setData('update_time', time());

            $model->setData('barcode_types', implode(',',$data['barcode_types']));

            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['template_id' => $model->getTemplateId(), '_current' => true]);
            }

            try {
                $this->templateRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the template.'));
                $this->dataPersistor->clear('pdfgenerator_template');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['template_id' => $model->getTemplateId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e->getMessage(), __('Something went wrong while saving the template.'));
            }

            $this->dataPersistor->set('pdfgenerator_template', $data);
            return $resultRedirect->setPath('*/*/edit', ['template_id' => $this->getRequest()->getParam('template_id')]);
        }
        return $resultRedirect->setPath('*/*/');
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

}