<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 */

namespace Magebright\CustomerApprove\Plugin\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ProductActions
 */
class Actions extends \Magento\Customer\Ui\Component\Listing\Column\Actions
{
    /**
     * Url path
     */
    const URL_PATH_APPROVE = 'magebright_customer_approve/customer/approved';
    const URL_PATH_REJECT = 'magebright_customer_approve/customer/reject';

    /**
     * Return data source with approve/reject options.
     * @param \Magento\Customer\Ui\Component\Listing\Column\Actions $subject
     * @param array                                                 $dataSource
     *
     * @return array
     */
    public function afterPrepareDataSource(
        \Magento\Customer\Ui\Component\Listing\Column\Actions $subject,
        $dataSource
    ) {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {
                $item['actions']['magebright_mc_approve'] = [
                    'href' => $this->urlBuilder->getUrl(
                        static::URL_PATH_APPROVE,
                        ['id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Approve'),
                ];

                $item['actions']['magebright_ca_reject'] = [
                    'href' => $this->urlBuilder->getUrl(
                        static::URL_PATH_REJECT,
                        ['id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Reject'),
                ];
            }
        }

        return $dataSource;
    }
}
