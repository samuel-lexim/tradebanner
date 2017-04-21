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

namespace Eadesigndev\Opicmsppdfgenerator\UI\Component\Sales\Order\Creditmemo\Masspdf;

class Pdftemplates implements \Zend\Stdlib\JsonSerializable
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var \Eadesigndev\Pdfgenerator\Model\ResourceModel\Pdfgenerator\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Additional options params
     *
     * @var array
     */
    private $data;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    private $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    private $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    private $additionalData = [];

    /**
     * @var \Eadesigndev\Opicmsppdfgenerator\Helper\Data
     */
    private $helper;

    /**
     * Pdftemplates constructor.
     * @param \Eadesigndev\Pdfgenerator\Model\ResourceModel\Pdfgenerator\CollectionFactory $collectionFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Eadesigndev\Pdfgenerator\Model\ResourceModel\Pdfgenerator\CollectionFactory $collectionFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Eadesigndev\Opicmsppdfgenerator\Helper\Data $helper,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {

        if(!$this->helper->isEnable()){
            return;
        }

        $i = 0;
        if ($this->options === null) {
            // get the massaction data from the database table
            $templateCollection = $this->collectionFactory
                ->create()
                ->addFieldToFilter('template_type', [
                    'eq' => \Eadesigndev\Opicmsppdfgenerator\Model\Source\TemplateType::TYPE_CREDIT_MEMO
                ])
                ->addFieldToFilter('is_active', [
                    'eq' => \Eadesigndev\Pdfgenerator\Model\Source\TemplateActive::STATUS_ENABLED
                ]);

            if (!count($templateCollection)) {
                return $this->options;
            }

            foreach ($templateCollection as $key => $badge) {
                $options[$i]['value'] = $badge->getData('template_id');
                $options[$i]['label'] = $badge->getData('template_name');
                $i++;
            }

            $this->prepareData();

            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => 'template_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }

            $this->options = array_values($this->options);
        }

        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    private function prepareData()
    {

        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}