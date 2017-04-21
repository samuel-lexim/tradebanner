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

namespace Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Custom\Items;

use Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Custom\AbstractCustomHelper;

class Items extends AbstractCustomHelper
{

    private $source;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
    }

    public function entity($source)
    {
        if (is_object($source)) {
            $this->source = $source;
            return $this;
        }

        $this->addTaxPercent();

        throw new \Exception(__('The source must be an object.'));
    }

    public function processAndReadVariables()
    {
        $this->addTaxPercent();
        $this->addItemOptitons();
        return $this->source;
    }

    private function addTaxPercent()
    {
        if (!$this->source instanceof \Magento\Sales\Model\Order\Item) {
            $orderItem = $this->source->getOrderItem();
        } else {
            $orderItem = $this->source;
        }

        $taxPercent = number_format($orderItem->getTaxPercent(), 2);

        $this->source->setData(
            \Magento\Sales\Api\Data\OrderItemInterface::TAX_PERCENT, $taxPercent
        );

        return $this->source;
    }

    private function addItemOptitons()
    {
        if (!$this->source instanceof \Magento\Sales\Model\Order\Item) {
            $orderItem = $this->source->getOrderItem();
        } else {
            $orderItem = $this->source;
        }

        $result = [];
        if ($options = $orderItem->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        $data = ''; $jobName = 'sad asd '; $turnAround = 'as dasd ';

        if (!empty($result)) {

            // Get Order Date
            $orderDate = $orderItem->getOrder()->getCreatedAt();

            $utcDate = $orderDate;
            $UTC = new \DateTimeZone("UTC");
            $losTZ = new \DateTimeZone("America/Los_Angeles");
            $date2 = new \DateTime($utcDate, $UTC);
            $date2->setTimezone($losTZ);   
   
            $h = intval($date2->format('H'));
            if ($h < 14) $date2->modify('+1 day');
            else $date2->modify('+2 day'); 
            $dueDate = $date2->format('F d, Y');
            // # end

            foreach ($result as $option => $value) {
                //$data .= $value['label'] . ' - ' . $value['value'] . '<br>';
                $label = $value['label'];
                $val = $value['value'];

                // Add inch text
                if (strtolower($label) == 'width' || strtolower($label) == 'height') $label .= ' (inch)';

                // Turn around: Insert Due Date
                if (strtolower($label) == 'turnaround') {
                    $valTmp = strtolower($val);
                    if (!(strpos($valTmp, 'next-day') === false && strpos($valTmp, 'next day') === false)) {
                        $val .= ' - (Due Date: ' . $dueDate . ')';
                    }
                    $turnAround = $label . ": " . $val;
                    $label = '<b>' . $label . '</b>';
                    $val = '<b>' . $val . '</b>';
                }

                // Job Name
                if (strtolower($label) == 'job name') {
                    $jobName = $label . ": " . $val;
                }

                $data .= '<tr><td>' . $label . '</td><td>' . $val . '</td></tr>';
            }
        }

        $this->source->setData(
            'item_options', $data
        );

        $this->source->setData(
            'job_name', $jobName
        );

        $this->source->setData(
            'turn_around', $turnAround
        );
    }

}