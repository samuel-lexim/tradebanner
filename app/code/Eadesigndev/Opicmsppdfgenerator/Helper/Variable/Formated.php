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

namespace Eadesigndev\Opicmsppdfgenerator\Helper\Variable;

use Magento\Framework\DataObject;
use Eadesigndev\Opicmsppdfgenerator\Helper\AbstractPDF;

class Formated extends \Magento\Framework\App\Helper\AbstractHelper
{

    private $order;

    /**
     * @var TimezoneInterface
     */
    private $timezoneInterface;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Formated constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    )
    {
        $this->order = $order;
        $this->dateTime = $dateTime;
        $this->timezoneInterface = $timezoneInterface;
        parent::__construct($context);
    }

    /**
     * Process object values for pdf output.
     * @param $object
     * @return DataObject|void
     */
    public function getFormated($object)
    {

        if (!is_object($object)) {
            return;
        }

        $objectData = $object->getData();

        $newData = [];
        foreach ($objectData as $data => $value) {

            if (is_array($value) || is_object($value)) {
                continue;
            }

            if (is_numeric($value) && !is_infinite($value)) {

                $newData[$data] = strip_tags($this->order->formatPrice($value));

                if ($data == 'qty' || strpos($data, 'qty') !== false) {
                    $newData[$data] = $value * 1;
                    continue;
                }

                continue;
            }

            if (in_array($data, AbstractPDF::DATE_FIELDS)) {
                $newData[$data] = $this->timezoneInterface->formatDate(
                    $this->timezoneInterface->date(new \DateTime($value)),
                    \IntlDateFormatter::MEDIUM,
                    true
                );

                continue;

            } else {
                $newData[$data] = $value;
            }
        }

        $eaInvoice = new DataObject($newData);

        return $eaInvoice;
    }

    /**
     * @param $object
     * @param $type
     * @return DataObject|void
     */
    public function getBarcodeFormated($object, $type)
    {
        if (!is_object($object)) {
            return;
        }

        $objectData = $object->getData();

        $newData = [];
        foreach ($objectData as $data => $value) {
            if (is_numeric($value) || is_string($value)) {
                $newData[$data] = strip_tags($value);
                $newData[$data] = '<barcode code="' . strip_tags($value) . '" type="' . $type . '" size="0.8" class="barcode" text="1" />';
                continue;
            }
        }

        $eaInvoice = new DataObject($newData);

        return $eaInvoice;
    }

    public function getZeroFormated($object)
    {
        if (!is_object($object)) {
            return;
        }

        $objectData = $object->getData();

        $newData = [];
        foreach ($objectData as $data => $value) {
            if (is_numeric($value)) {
                if ($value != 0) {
                    $newData[$data] = $value;
                    continue;
                }
            }
        }

        $eaInvoice = new DataObject($newData);

        return $eaInvoice;
    }

    public function getItemsArea($template, $start, $end)
    {
        //todo refactor this part using regular expression and add validations
        //todo add the validation or there will be a fata error without the items

        if (strpos($template, $start) === false) {
            return [$template, '', ''];
        }

        if (strpos($template, $end) === false) {
            return [$template, '', ''];
        }

        $firstPart = explode($start, $template);

        $beginning = $firstPart[0];

        $secondPart = explode($end, $firstPart[1]);

        $items = $secondPart[0];

        $end = $secondPart[1];

        return [$beginning, $items, $end];
    }

}
