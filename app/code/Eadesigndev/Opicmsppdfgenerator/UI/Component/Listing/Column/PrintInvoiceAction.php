<?php
/**
 * @author Samuel Kong
 * @company Lexim IT
 * @date Mar 17 2017
 * @email samuel.kong@leximit.com
 * @contact via skype: letunhatkong
 */

namespace Eadesigndev\Opicmsppdfgenerator\UI\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

use Magento\Sales\Model\Order\InvoiceRepository;

/**
 * Class ViewAction
 */

class PrintInvoiceAction extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    protected $invoiceRepository;

    /**
     * PrintInvoiceAction constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param InvoiceRepository $invoiceRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        InvoiceRepository $invoiceRepository,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->invoiceRepository = $invoiceRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {

                    $invoice = $this->invoiceRepository->get($item['entity_id']);

                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                    $orderIdParamName = $this->getData('config/orderIdParamName');
                    $templateIdParamName = $this->getData('config/templateIdParamName');
                    $invoiceIdParamName = $this->getData('config/invoiceIdParamName');

                    $item[$this->getData('name')] = [
                        'view' => [
                            'href' => $this->urlBuilder->getUrl(
                                $viewUrlPath,
                                [
                                    $invoiceIdParamName  => $item['entity_id'],
                                    $templateIdParamName => 5,
                                    $orderIdParamName =>$invoice->getOrder()->getId()
                                ]
                            ),
                            'label' => __('Print')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }


}
