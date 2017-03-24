<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessLayoutRenderElement implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $event = $observer->getEvent();
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $event->getLayout();

        $name = $event->getElementName();
        $block = $layout->getBlock($name);
        $transport = $event->getTransport();
        
        if (($block instanceof \Magento\Checkout\Block\Cart && ($block->getNameInLayout() == 'checkout.cart' || $event->getElementName() == 'checkout.root')) || ($block instanceof \Magento\Checkout\Block\Onepage && ($block->getNameInLayout() == 'checkout.root' || $event->getElementName() == 'checkout.root'))) {
            $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Rewardcoupon');
            $block->setTemplate('onepage.phtml');
            $block->setNameInLayout("j2t_checkout_content");
            $extraHtml = $block->toHtml();
            $output = $transport->getData('output');

            $transport->setData('output', $output . $extraHtml);
        }
        
        if (!$objectManager->get('J2t\Rewardpoints\Helper\Data')->getActive()){
            return $this;
        }

        if (($block instanceof \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable) 
                && ($block->getNameInLayout() == 'product.info.options.configurable' 
                        || $event->getElementName() == 'product.info.options.configurable'
                        /*|| $block->getNameInLayout() == 'product.info.configurable' 
                        || $event->getElementName() == 'product.info.configurable'*/)
                        //TODO: use colorswatch swatchRenderer >> 'product.info.configurable'
                ) {
            //options_configurable
            $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Product\View\Type\Configurable');
            $block->setTemplate('product/view/type/options/configurable.phtml');
            $block->setNameInLayout("point_info_details_configurable_js");
            $extraHtml = $block->toHtml();
            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        } else if (($block instanceof \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable) 
                && ($block->getNameInLayout() == 'product.info.configurable' 
                        || $event->getElementName() == 'product.info.configurable')
                        
                ) {
            //options_configurable
            $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Product\View\Type\Configurable');
            $block->setTemplate('product/view/type/options/configColorswatch.phtml');
            $block->setNameInLayout("point_info_details_configurable_js");
            $extraHtml = $block->toHtml();
            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        }

        if (($block instanceof \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle) && ($block->getNameInLayout() == 'product.info.bundle.options' || $event->getElementName() == 'product.info.bundle.options')) {
            //options_configurable
            $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Product\View\Type\Bundle');
            $block->setTemplate('product/view/type/options/bundle.phtml');
            $block->setNameInLayout("point_info_details_bundle_js");
            $extraHtml = $block->toHtml();
            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        }

        /*
         * customize.button
         * product.info.addto
         */
        if (($block instanceof \Magento\Catalog\Block\Product\View\Interceptor) && ($block->getNameInLayout() == 'product.info.addtocart.additional' || $event->getElementName() == 'product.info.addtocart.additional' || $block->getNameInLayout() == 'product.info.addto' || $event->getElementName() == 'product.info.addto' || $block->getNameInLayout() == 'product.info.addto.bundle' || $event->getElementName() == 'product.info.addto.bundle')) {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $show = $objectManager->get('J2t\Rewardpoints\Helper\Data')->showOnProductView();
            if ($show) {
                $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Pointinfo');
                $block->setData('product', $block->getProduct());
                $block->setData('from_list', false);

                $block->setTemplate('point_info.phtml');
                $extraHtml = $block->toHtml();

                $output = $transport->getData('output');
                $transport->setData('output', $extraHtml . $output);
            }
        }

        if (($block instanceof \Magento\Checkout\Block\Cart\Coupon) && ($block->getNameInLayout() == 'checkout.cart.coupon' || $event->getElementName() == 'checkout.cart.coupon')) {
            $html = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Rewardcoupon');
            $html->setTemplate('reward_coupon.phtml');
            $html->setNameInLayout("reward_coupon");

            $extraHtml = $html->toHtml();

            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        }

        if (($block instanceof \Magento\Framework\View\Element\AbstractBlock) && ($block->getNameInLayout() == 'customer_account_dashboard_top' || $event->getElementName() == 'customer_account_dashboard_top')) {

            $extraHtml = '...Point Dashboard...';
            $dashboardHtml = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Dashboard');
            $dashboardHtml->setTemplate('dashboard_points.phtml');
            $dashboardHtml->setNameInLayout("customer_account_points");

            $extraHtml = $dashboardHtml->toHtml();

            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        }
        
//        if (($block instanceof \Magento\Backend\Block\Dashboard\Diagrams) /*&& ($block->getNameInLayout() == 'customer_account_dashboard_top' || $event->getElementName() == 'customer_account_dashboard_top')*/) {
//            
//            if ($diagrams = $block->getChild('diagrams')) {
//                $extraHtml = '...Admin Point Dashboard...';
//                /*$dashboardHtml = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Dashboard');
//                $dashboardHtml->setTemplate('dashboard_points.phtml');
//                $dashboardHtml->setNameInLayout("customer_account_points");
//
//                $extraHtml = $dashboardHtml->toHtml();*/
//
//                $output = $transport->getData('output');
//                $transport->setData('output', $output . $extraHtml);
//            }
//        }
        
    }

}
