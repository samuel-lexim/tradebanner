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

namespace Eadesigndev\Opicmsppdfgenerator\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output
     */
    private $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output $helper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Customer\Model\Session$customerSession
    )
    {
        parent::__construct($context);
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->helper = $helper;
        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->customerSession = $customerSession;
    }

    /**
     * Print the users PDF
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam('template_id');

        if (!$templateId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $templateModel = $this->_objectManager->create('Eadesigndev\Pdfgenerator\Api\TemplatesRepositoryInterface')->getById($templateId);
        if (!$templateModel) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $templateType = $templateModel->getData('template_type');

        $templateTypeName = \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPES[$templateType];

        $collection = $this->collection($templateTypeName);

        if (!count($collection)) {
            return;
        }

        if (empty($collection)) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $helper = $this->helper;

        foreach ($collection as $source) {

            if($source instanceof \Magento\Sales\Model\Order){
                $customerId = $source->getCustomerId();
            } else {
                $customerId = $source->getOrder()->getCustomerId();
            }

            if ($customerId) {
                $pseudoCustomer = $this->customer($customerId);
                $helper->setCustomer($pseudoCustomer);
            }

            if ($this->customerSession->getCustomer()->getId() != $customerId) {
                return $this->resultForwardFactory->create()->forward('noroute');
            }

            $helper->setSource($source);
            $helper->setTemplate($templateModel);

            $pdfFileData = $helper->template2Pdf();
        }

        $output = $helper->PDFmerger();

        $date = $this->dateTime->date('Y-m-d_H-i-s');

        $fileName = $pdfFileData['filename'] . $date . '.pdf';

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

        $customerData = $this->extensibleDataObjectConverter->toFlatArray(
            $customer,
            [],
            '\Magento\Customer\Api\Data\CustomerInterface'
        );

        $pseudoCustomer = new DataObject($customerData);
        return $pseudoCustomer;
    }

    /**
     * @param $templateTypeName
     * @return mixed
     */
    protected function collection($templateTypeName)
    {
        $this->criteriaBuilder->addFilters(
            [$this->filterBuilder
                ->setField('entity_id')
                ->setValue($this->getRequest()->getParam('source_id'))
                ->setConditionType('eq')
                ->create()]
        );
        $searchCriteria = $this->criteriaBuilder->create();

        $collection = $this->_objectManager->create(
            'Magento\Sales\Api\\' .
            ucfirst($templateTypeName) .
            'RepositoryInterface')->getList($searchCriteria);
        return $collection;
    }
}