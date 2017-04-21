<?php

namespace J2t\Rewardpoints\Block;

class Pointinfo extends \Magento\Framework\View\Element\Template {

    protected $_template = 'point_info.phtml';
    protected $_pointData;
    protected $_customerSession, $_coreRegistry;
    protected $_minPoints, $_maxPoints;
    protected $localeFormat;
    protected $jsonEncoder;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Framework\Registry $registry, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Framework\Json\EncoderInterface $jsonEncoder, array $data = []
    ) {
        $this->_pointData = $pointHelper;
        $this->_customerSession = $customerSession;
        $this->_coreRegistry = $registry;
        $this->localeFormat = $localeFormat;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function showImage() {
        $rewardHelper = $this->_pointData;
        return $rewardHelper->getShowSmallImage();
    }

    public function sizeImage() {
        $rewardHelper = $this->_pointData;
        return $rewardHelper->getSizeSmallImage();
    }

    public function getImageUrl() {
        $rewardHelper = $this->_pointData;
        return $rewardHelper->getURLSmallImage();
    }

    public function isUpTo() {
        if ($this->getProduct()->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return true;
        }
        return false;
    }

    public function getJSMathMethod() {
        $value = 'total_points';
        switch ($this->_pointData->getMathMethodCatalogPages()) {
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_CEIL:
                $value = 'Math.ceil(total_points)';
                break;
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_FLOOR:
                $value = 'Math.floor(total_points)';
                break;
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_ROUND:
                $value = 'Math.round(total_points)';
                break;
        }
        return $value;
    }

    public function mathActionOnCatalogPages($value) {
        return $this->_pointData->mathActionOnCatalogPages($value);
    }

    public function getProductPointsRange() {
        if (!$this->getFromList()) {
            return [];
        }
        $rewardHelper = $this->_pointData;
        return $rewardHelper->getProductPointsRange($this->getProduct());
    }

    public function getProductPoints() {
        //return $this->getProduct()->getId();
        $rewardHelper = $this->_pointData;
        return $rewardHelper->getProductPoints($this->getProduct());
    }

    public function getProduct() {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    public function isAllowedCalculationQty() {
        return ($this->getProduct()->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE && $this->getProduct()->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE);
    }

    public function showEquivalence() {
        return $this->_pointData->getShowEquivalence();
    }

    public function getPointsEquivalence($points) {
        return $this->_pointData->getPointMoneyEquivalence($points);
    }

    public function getDefaultPointMoneyEquivalence() {
        return $this->_pointData->getPointMoneyEquivalence(1, true);
    }

    public function priceFormat() {
        $config = $this->localeFormat->getPriceFormat();
        return $this->jsonEncoder->encode($config);
    }

}
