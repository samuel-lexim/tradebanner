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

namespace Eadesigndev\Opicmsppdfgenerator\Block\Adminhtml\Sales\Order;

class PrintPDF extends \Magento\Backend\Block\Widget\Container
{
    private $lastItem = [];

    /**
     * @var \Eadesigndev\Opicmsppdfgenerator\Helper\Data
     */
    private $dataHelper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Data $dataHelper,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        if (!$this->dataHelper->isEnable(\Eadesigndev\Opicmsppdfgenerator\Helper\Data::ENABLE_ORDER)){
            return $this;
        }

        $lastItem = $this->dataHelper->getTemplateStatus(
            $this->coreRegistry->registry('sales_order'),
            \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPE_ORDER);

        if (empty($lastItem->getId())) {
            return;
        }
        $this->lastItem = $lastItem;

//        $this->addButton(
//            'eadesign_print',
//            [
//                'label' => 'Print',
//                'class' => 'print',
//                'onclick' => 'setLocation(\'' . $this->getPdfPrintUrl() . '\')'
//            ]
//        );

        // Samuel Kong
        $this->addButton(
            'eadesign_print',
            [
                'label' => 'Print',
                'class' => 'print samuel',
                'onclick' => 'window.open(\'' . $this->getPdfPrintUrl() . '\',\'_blank\')'
            ]
        );

        parent::_construct();
    }

    /**
     * @return string
     */
    public function getPdfPrintUrl()
    {
        return $this->getUrl(
            'opicmsppdfgenerator/*/printpdf',
            [
                'template_id' => $this->lastItem->getId(),
                'order_id' => $this->getOrderId(),
            ]);

    }

    /**
     * @return integer
     */
    public function getOrderId()
    {
        return $this->coreRegistry->registry('sales_order')->getId();
    }
}
