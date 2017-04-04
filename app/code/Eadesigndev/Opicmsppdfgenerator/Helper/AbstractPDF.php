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

namespace Eadesigndev\Opicmsppdfgenerator\Helper;

use Eadesigndev\Pdfgenerator\Model\Template\Processor;

abstract class AbstractPDF extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Paper orientation
     */
    const PAPER_ORI = [
        1 => 'P',
        2 => 'L'
    ];

    /**
     * Paper size
     */
    const PAPER_SIZE = [
        1 => 'A4-',
        2 => 'A3-',
        3 => 'A5-',
        4 => 'A6-',
        5 => 'LETTER-',
        6 => 'LEGAL-'
    ];

    /**
     * Object date fields
     */
    const DATE_FIELDS = [
        'created_at',
        'updated_at'
    ];

    const CODE_BAR = [
        'ean13',
        'isbn',
        'issn',
        'upca',
        'upce',
        'ean8',
        'imb',
        'rm4scc',
        'kix',
        'postnet',
        'planet',
        'c128a',
        'c128b',
        'c128c',
        'ean128a',
        'ean128b',
        'ean128c',
        'c39',
        'c39+',
        'c39e',
        'c39e+',
        's25',
        's25+',
        'i25',
        'i25+',
        'i25b',
        'i25b+',
        'c93',
        'msi',
        'msi+',
        'codabar',
        'code11',
        'QR'
    ];

    /**
     * @var $context
     */
    public $context;

    /**
     * @var Processor
     */
    public $processor;

    /**
     * @var
     */
    protected $order;

    /**
     * @var invoice;
     */
    protected $source;

    /**
     * @var template
     */
    protected $template;

    /**
     * @var the customer data fom the order
     */
    public $customer;
    /**
     * @var IdentityInterface
     */
    protected $identityContainer;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * AbstractPDF constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Processor $processor
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Sales\Model\Order\Email\Container\InvoiceIdentity $identityContainer
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Eadesigndev\Pdfgenerator\Model\Template\Processor $processor,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\Order\Email\Container\InvoiceIdentity $identityContainer,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
    )
    {
        $this->processor = $processor;
        $this->paymentHelper = $paymentHelper;
        $this->identityContainer = $identityContainer;
        $this->addressRenderer = $addressRenderer;
        parent::__construct($context);
    }

    /**
     * @param $source
     * @return $this
     */
    public function setSource($source)
    {        
        $this->source = $source;

        if ($source instanceof \Magento\Sales\Model\Order) {
            $this->setOrder($source);
        } else {
            $this->setOrder($source->getOrder());
        }

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function setOrder(\Magento\Sales\Model\Order $order)
    {     
        // Samuel Kong
        $utcDate = $order->getCreatedAt();
        $UTC = new \DateTimeZone("UTC");
        $losTZ = new \DateTimeZone("America/Los_Angeles");
        $date = new \DateTime($utcDate, $UTC );
        $date->setTimezone( $losTZ );
        //$order->setCreatedAt( $date->format('Y-m-d H:i:s') );
        // # Samuel Kong
        $this->order = $order;
        return $this;
    }

    /**
     * @param \Eadesigndev\Pdfgenerator\Model\Pdfgenerator $template
     * @return $this
     */
    public function setTemplate(\Eadesigndev\Pdfgenerator\Model\Pdfgenerator $template)
    {
        $this->template = $template;
        $this->processor->setPDFTemplate($template);
        return $this;
    }

    /**
     * @param \Magento\Framework\DataObject $customer
     * @return $this
     */
    public function setCustomer(\Magento\Framework\DataObject $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return mixed
     */
    protected function getPaymentHtml(\Magento\Sales\Model\Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return null
     */
    protected function getFormattedShippingAddress(\Magento\Sales\Model\Order $order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return mixed
     */
    protected function getFormattedBillingAddress(\Magento\Sales\Model\Order $order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * Get the format and orientation, ex: A4-L
     * @param $form
     * @param $ori
     * @return string
     */
    public function paperFormat($form, $ori)
    {
        $size = self::PAPER_SIZE;
        $oris = self::PAPER_ORI;

        if ($ori == \Eadesigndev\Pdfgenerator\Model\Source\TemplatePaperOrientation::TEMAPLATE_PAPER_PORTRAIT) {
            return str_replace('-', '', $size[$form]);
        }

        $format = $size[$form] . $oris[$ori];

        return $format;
    }
}
