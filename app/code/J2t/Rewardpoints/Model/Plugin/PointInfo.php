<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model\Plugin;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;

class PointInfo {

    protected $_currentProduct;

    public function beforeGetProductPrice($subject, $product) {
        $this->_currentProduct = $product;
    }

    public function afterGetProductPrice($subject, $proceed) {
        //$this->_currentProduct->getId()
        //$product_price = $this->_currentProduct->getFinalPrice();
        $extraHtml = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $show = $objectManager->get('J2t\Rewardpoints\Helper\Data')->showOnProductList();
        if ($show) {
            $magento_block = $objectManager->get('Magento\Framework\View\Element\BlockFactory');
            $block = $magento_block->createBlock('J2t\Rewardpoints\Block\Pointinfo');
            $block->setData('product', $this->_currentProduct);
            $block->setData('from_list', true);

            $block->setTemplate('point_info.phtml');
            $block->setNameInLayout("point_info_details");

            $extraHtml = $block->toHtml();
        }

        return $proceed . $extraHtml;
    }

}
