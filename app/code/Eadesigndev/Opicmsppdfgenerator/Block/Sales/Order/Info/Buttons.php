<?php


/**
 * Block of links in Order view page
 */
namespace Eadesigndev\Opicmsppdfgenerator\Block\Sales\Order\Info;

use Magento\Customer\Model\Context;

class Buttons extends \Magento\Sales\Block\Order\Info\Buttons
{
    /**
     * @var \Magento\Sales\Model\Order\Invoice
     */
    private $lastitem;

    /**
     * @var string
     */
    protected $_template = 'Eadesigndev_Opicmsppdfgenerator::Order/Info/buttons.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [],
        \Eadesigndev\Opicmsppdfgenerator\Helper\Data $helper
    )
    {
        parent::__construct($context, $registry, $httpContext, $data);
        $this->helper = $helper;
    }

    /**
     * @param $source
     * @return bool
     */
    public function addPDFLink($source)
    {
        $helper = $this->helper;

        if ($helper->isEnable()) {
            $lastItem = $helper->getTemplateStatus(
                $source
            );

            if (!empty($lastItem->getId())) {
                $this->lastitem = $lastItem;
                return true;
            }
        }

        return false;
    }

    /**
     * @param $source
     * @return string
     */
    public function getPrintPDFUrl($source)
    {
        return $this->getUrl('opicmsppdfgenerator/index/index', [
            'template_id' => $this->lastitem->getId(),
            'order_id' => $source->getId(),
            'source_id' => $source->getId()
        ]);
    }
}
