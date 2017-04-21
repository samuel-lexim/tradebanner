<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model\Plugin;

class AjaxBlock {

    protected $_checkoutSession;
    protected $_productModel;
    protected $_catalogProductVisibility;
    protected $_catalogConfig;
    protected $_imageHelper;
    protected $_objectManager;
    protected $_response;
    protected $helper;
    
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Block\Product\Context $context,
        \J2t\Rewardpoints\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Response\Http $response
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_productModel = $productModel;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_catalogConfig = $context->getCatalogConfig();
        $this->_imageHelper = $context->getImageHelper();
        $this->_response = $response;
        $this->helper = $helper;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    
    public function aroundExecute(\Magento\Backend\Controller\Adminhtml\Dashboard\AjaxBlock $subject, \Closure $proceed)
    {
        $output = '';
        $blockTab = $subject->getRequest()->getParam('block');
        $blockClassSuffix = str_replace(
            ' ',
            '\\',
            ucwords(str_replace('_', ' ', $blockTab))
        );
        //if (in_array($blockTab, ['tab_orders', 'tab_amounts', 'totals'])) {
        if (in_array($blockTab, ['tab_gather', 'tab_spend'])) {
            $output = $this->layoutFactory->create()
                ->createBlock('J2t\\Rewardpoints\\Block\\Adminhtml\\Dashboard\\' . $blockClassSuffix)
                //->createBlock('Magento\\Backend\\Block\\Dashboard\\' . $blockClassSuffix)
                ->toHtml();
            /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            return $resultRaw->setContents($output);
        }
        
        //before
        $returnValue = $proceed(); // it get you old function return value
        //after
        
        return $returnValue; // if its object make sure it return same object which you addition data
    }
    
}
