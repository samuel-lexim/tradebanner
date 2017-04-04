<?php
/**
 * @author Duy Nguyen, Samuel Kong
 * @company Lexim IT
 * @date Mar 17 2017
 * @email duy.nguyen@leximit.com, samuel.kong@leximit.com
 * @contact via skype: letunhatkong
 */

namespace Eadesigndev\Opicmsppdfgenerator\UI\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;

/**
 * Class ViewAction
 */

class PrintAction extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
	protected $actionUrlBuilder;

    /**
     * PrintAction constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
		UrlBuilder $actionUrlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
		$this->actionUrlBuilder = $actionUrlBuilder;
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

                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                    $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'entity_id';
                    $templateIdParamName = $this->getData('config/templateIdParamName') ?: 'entity_id';

                    $item[$this->getData('name')] = [
                        'view' => [
                            'href' => $this->actionUrlBuilder->getUrl(
							$this->urlBuilder->getUrl(
                                $viewUrlPath,
                                [
                                    $urlEntityParamName => $item['entity_id'],
                                    $templateIdParamName => 8
                                ]
                            ), isset($item['_first_store_id']) ? $item['_first_store_id'] : null, isset($item['store_code']) ? $item['store_code'] : null),
                            'label' => __('Print')
                        ]
                    ];


                }
            }
        }

        return $dataSource;
    }


}
