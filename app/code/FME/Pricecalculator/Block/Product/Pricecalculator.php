<?php
namespace FME\Pricecalculator\Block\Product;
class Pricecalculator extends \Magento\Catalog\Block\Product\View 
{
    public $urlBuilder;
    public $storeManager;
    public $pricecalculatorHelper;    
    protected $_fileDriver;
    protected $_objectManager;
    
    public function __construct(\Magento\Catalog\Block\Product\Context $context,
            \Magento\Framework\Url\EncoderInterface $urlEncoder,
            \Magento\Framework\Json\EncoderInterface $jsonEncoder,
            \Magento\Framework\Stdlib\StringUtils $string,
            \Magento\Catalog\Helper\Product $productHelper,
            \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
            \Magento\Framework\Locale\FormatInterface $localeFormat,
            \Magento\Customer\Model\Session $customerSession,
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
            \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
            \FME\Pricecalculator\Helper\Data $pricecalculatorData,
            \Magento\Framework\Filesystem\Driver\File $fileDriver,
            \Magento\Framework\ObjectManagerInterface $objectManager,
            array $data = []) {
        
        $this->urlBuilder = $context->getUrlBuilder();
        $this->storeManager = $context->getStoreManager();
        $this->pricecalculatorHelper = $pricecalculatorData;
        $this->_fileDriver = $fileDriver;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
    }
    
    public function getProductPricingRule($product = null){
        if ($product == null) {
            $product = $this->getProduct();
        }
        $pricingRule = [];
        $data = explode(';', $product->getPricingRule());
        
        foreach ($data as $item) {
            
            preg_match_all("/ ([^=]+) = ([^\\s]+) /x", $item, $p);
            $pair = array_combine($p[1], $p[2]);

            if (isset($pair['discount'])) {
                $pricingRule['discount'] = explode(',', $pair['discount']);
            }

            if (isset($pair['size'])) {
                $pricingRule['size'] = explode(',', $pair['size']);
            }

        }
        //area or volume
        if (in_array('area', $data)) {
            $pricingRule['by'] = 'area';
        }
        if (in_array('volume', $data)) {
            $pricingRule['by'] = 'volume';
        }
        // discount type
        if (in_array('percent', $data)) {
            $pricingRule['type'] = 'percent';
        }
        if (in_array('fixed', $data)) {
            $pricingRule['type'] = 'fixed';
        }
            
        return $pricingRule;
    }
}
