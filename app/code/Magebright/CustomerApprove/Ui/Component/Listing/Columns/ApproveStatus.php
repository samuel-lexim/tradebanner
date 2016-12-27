<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
  */

namespace Magebright\CustomerApprove\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magebright\CustomerApprove\Model\Customer\Attribute\Source\Approveoptions;

class ApproveStatus extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Approveoptions
     */
    protected $approveOptions;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param CustomerFactory $customerRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        CustomerFactory $customerFactory,
        Approveoptions $approveOptions,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerFactory = $customerFactory;
        $this->approveOptions = $approveOptions;

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
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $customer = $this->customerFactory->create()->load($item['entity_id']);

                $item[$fieldName . '_html'] = sprintf(
                    '<span class="grid-severity-%s"><span>%s</span></span>',
                    $this->approveOptions->getApproveStatusHtmlClass($customer->getApproveStatus()),
                    $this->approveOptions->getOptionText($customer->getApproveStatus())
                );
            }
        }

        return $dataSource;
    }
}
