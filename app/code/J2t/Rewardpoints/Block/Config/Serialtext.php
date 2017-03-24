<?php

namespace J2t\Rewardpoints\Block\Config;

class Serialtext extends \Magento\Config\Block\System\Config\Form\Field { 
	
    protected $_jsonEncoder;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_jsonEncoder = $jsonEncoder;
    }
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $extra = '';
        if ($current_store = $this->getStore()){
            $store = $current_store;

            $url = parse_url($store->getBaseUrl());
            $host = $url['host'];

            $extra = '<div>'.__("Serial defined for store domain '%1'", $host).'</div>';
        } else {
            $store = $this->_storeManager->getWebsite(
                                    $this->getWebsite()
                                )->getDefaultStore();

            $url = parse_url($store->getBaseUrl());
            $host = $url['host'];
            $extra = '<div>'.__("Serial defined for store domain '%1'", $host).'</div>';
        }

        $html = $extra;
        return parent::_getElementHtml($element).$html;
    }
}

