<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model\Plugin;

class Block {

    protected $_checkoutSession;
    protected $_productModel;
    protected $_catalogProductVisibility;
    protected $_catalogConfig;
    protected $_imageHelper;
    protected $_objectManager;
    protected $_response;
    protected $helper;
    
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Block\Product\Context $context,
        \J2t\Rewardpoints\Helper\Data $helper,
        //\Magento\Catalog\Helper\Image $imageHelper,
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
    }

    
    public function aroundSetChild(\Magento\Framework\View\Element\AbstractBlock $subject, \Closure $proceed, $alias, $block)
    {
        // if ($this->helper->showAdminDashboard()){
        if (false) {
            if ($alias == 'diagrams'){
                $block->addTab('gather', 
                    [
                    'label' => __('Gathered Points'),
                    'content' => $block->getLayout()->createBlock('J2t\Rewardpoints\Block\Adminhtml\Dashboard\Tab\Gather')->toHtml(),
                    ]
                );

                $block->addTab('spend',
                    [
                    'label' => __('Points Used'),
                    'content' => $block->getLayout()->createBlock('J2t\Rewardpoints\Block\Adminhtml\Dashboard\Tab\Spend')->toHtml(),
                    ]
                );
            }
        }
        //before
        $returnValue = $proceed($alias, $block); // it get you old function return value
        //after
        
        return $returnValue; // if its object make sure it return same object which you addition data
    }
    
    public function aroundToHtml(\Magento\Framework\View\Element\AbstractBlock $parentBlock, \Closure $proceed)
    {
        $extraHtml = '';
        if ($this->helper->showAdminDashboard()){
            if($parentBlock instanceof \Magento\Backend\Block\Dashboard\Totals){
                $block = $parentBlock->getLayout()->createBlock('J2t\Rewardpoints\Block\Adminhtml\Dashboard\Totals');
                $block->setTemplate('dashboard/totalbar.phtml');
                $block->setNameInLayout("j2t_rewardpoints_totalbar");
                $extraHtml = $block->toHtml();
                //echo $extraHtml;
            }
        }
        //before
        $returnValue = $proceed(); // it get you old function return value
        //after
        
        return $returnValue.$extraHtml; // if its object make sure it return same object which you addition data
    }
    
}
