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

namespace Eadesigndev\Opicmsppdfgenerator\Model\Email;

use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output;
use Eadesigndev\Opicmsppdfgenerator\Helper\Data;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Eadesigndev\Pdfgenerator\Model\Email\TransportBuilder;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{

    /**
     * @var Pdf
     */
    protected $helper;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * SenderBuilder constructor.
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param TransportBuilder $transportBuilder
     * @param Output $helper
     * @param Data $dataHelper
     * @param DateTime $dateTime
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors\Output $helper,
        Data $dataHelper,
        DateTime $dateTime
    )
    {
        $this->helper = $helper;
        $this->dataHelper = $dataHelper;
        $this->dateTime = $dateTime;
        parent::__construct($templateContainer, $identityContainer, $transportBuilder);
    }

    /**
     * Add attachment to the main mail
     */
    public function send()
    {
        $vars = $this->templateContainer->getTemplateVars();
        $this->_checkSource($vars);

        parent::send();
    }

    /**
     * Add attachment to the css/bcc mail
     */
    public function sendCopyTo()
    {
        $vars = $this->templateContainer->getTemplateVars();
        $this->_checkSource($vars);

        parent::sendCopyTo();
    }

    /**
     *
     * Check if we need to send the invoice email
     *
     * @param $vars
     * @return $this
     */
    private function _checkSource($vars)
    {

        $templateId = $this->templateContainer->getTemplateId();

        $helper = $this->helper;

        if ($templateId == \Eadesigndev\Opicmsppdfgenerator\Controller\Adminhtml\Variable\Template::ORDER_TMEPLTE_ID) {

            if (!$this->dataHelper->isEmail(\Eadesigndev\Opicmsppdfgenerator\Helper\Data::EMAIL_ORDER)) {
                return $this;
            }

            $source = $vars['order'];
            $helper->setSource($source);
            $template = $this->dataHelper->getTemplateStatus(
                $source,
                \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPE_ORDER);
        }

        if ($templateId == \Eadesigndev\Opicmsppdfgenerator\Controller\Adminhtml\Variable\Template::INVOICE_TMEPLTE_ID) {

            if (!$this->dataHelper->isEmail()) {
                return $this;
            }

            $source = $vars['invoice'];
            $helper->setSource($source);
            $template = $this->dataHelper->getTemplateStatus(
                $source,
                \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPE_INVOICE);

        }

        if ($templateId == \Eadesigndev\Opicmsppdfgenerator\Controller\Adminhtml\Variable\Template::SHIPMENT_TMEPLTE_ID) {

            if (!$this->dataHelper->isEmail(\Eadesigndev\Opicmsppdfgenerator\Helper\Data::EMAIL_SHIPMENT)) {
                return $this;
            }

            $source = $vars['shipment'];
            $helper->setSource($source);
            $template = $this->dataHelper->getTemplateStatus(
                $source,
                \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPE_SHIPMENT);
        }

        if ($templateId == \Eadesigndev\Opicmsppdfgenerator\Controller\Adminhtml\Variable\Template::CREDITMEMO_TMEPLTE_ID) {

            if (!$this->dataHelper->isEmail(\Eadesigndev\Opicmsppdfgenerator\Helper\Data::EMAIL_CREDITMEMO)) {
                return $this;
            }

            $source = $vars['creditmemo'];
            $helper->setSource($source);
            $template = $this->dataHelper->getTemplateStatus(
                $source,
                \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPE_CREDIT_MEMO);
        }

        if (!$template->getData('template_type')) {
            return $this;
        }

        $helper->setTemplate($template);

        $pdfFileData = $helper->template2Pdf();
        $output = $helper->PDFmerger();

        $date = $this->dateTime->date('Y-m-d_H-i-s');

        $this->transportBuilder->addAttachment(
            $output
            , \Zend_Mime::TYPE_OCTETSTREAM
            , \Zend_Mime::DISPOSITION_ATTACHMENT
            , \Zend_Mime::ENCODING_BASE64
            , $pdfFileData['filename'] . $date . '.pdf'
        );

        return $this;
    }

}
