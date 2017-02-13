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

namespace Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Processors;

use Eadesigndev\Opicmsppdfgenerator\Helper\Abstractpdf;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\DataObject;

class Items extends AbstractHelper
{
    private $formated;

    private $customData;

    /**
     * @var Processor
     */
    public $processor;

    /**
     * Items constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Eadesigndev\Pdfgenerator\Model\Template\Processor $processor
     * @param \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Formated $formated
     * @param \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Custom\Items\Items $customData
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Eadesigndev\Pdfgenerator\Model\Template\Processor $processor,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Formated $formated,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Variable\Custom\Items\Items $customData
    )
    {
        $this->formated = $formated;
        $this->customData = $customData;
        $this->processor = $processor;
        parent::__construct($context);
    }

    /**
     * @param $standardItem
     * @param $template
     * @return string
     */
    public function variableItemProcessor($standardItem, $template)
    {

        $item = $this->customData->entity($standardItem)->processAndReadVariables();

        $transport = [
            'item' => $item,
            'ea_item' => $this->formated->getFormated($item),
            'ea_item_if' => $this->formated->getZeroFormated($item),
            'order.item' => $this->orderItem($item),
            'order.ea_item' => $this->formated->getFormated($this->orderItem($item)),
            'order.ea_item_if' => $this->formated->getZeroFormated($this->orderItem($item)),
        ];

        foreach (AbstractPDF::CODE_BAR as $code) {
            $transport['ea_barcode_' . $code . '_item'] = $this->formated->getBarcodeFormated($item, $code);
            $transport['ea_barcode_' . $code . '_order.item'] = $this->formated->getBarcodeFormated($this->orderItem($item), $code);
        }

        $processor = $this->processor;

        $processor->setVariables($transport);
        $processor->setTemplate($template);

        $parts = $processor->processTemplate();

        return $parts;

    }

    /**
     * @param $source
     * @param $templateModel
     * @return string
     */
    public function processItems($source, $templateModel)
    {

        $items = $source->getItems();

        $templateBodyParts = $this->formated->getItemsArea($templateModel->getData('template_body'), '##productlist_start##', '##productlist_end##');
        $itemHtml = '';

        $i = 1;
        foreach ($items as $item) {
            $item->setData('shipping', $source->getBaseShippingAmount());
            $item->setData('position', $i++);

            if ($item instanceof \Magento\Sales\Model\Order\Item) {
                if ($parentItem = $item->getParentItem()) {
                    if ($parentItem->getData('product_type') != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                        continue;
                    } else {
                        $item->setData('position', '');
                    }
                }

            } else {
                if ($parentItem = $item->getOrderItem()->getParentItem()) {
                    if ($parentItem->getData('product_type')  != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                        continue;
                    }
                    $item->setData('position', '');
                }
            }

            $itemBodyParts = new DataObject(['template_body' => $templateBodyParts[1]]);
            $processedItem = $this->variableItemProcessor($item, $itemBodyParts);
            $itemHtml .= $processedItem['body'];
        }

        $template = $templateBodyParts[0] . $itemHtml . $templateBodyParts[2];

        return $template;

    }

    /**
     * @return $this
     */
    private function orderItem($item)
    {
        if (!$item instanceof \Magento\Sales\Model\Order\Item) {
            $orderItem = $item->getOrderItem();
            $item = $this->customData->entity($orderItem)->processAndReadVariables();
            return $item;
        }

        return $item;
    }

}