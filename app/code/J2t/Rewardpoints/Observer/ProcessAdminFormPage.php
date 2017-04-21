<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessAdminFormPage implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
		$block              = $observer->getBlock();
		$request = $block->getRequest();
		
		if (($block->getNameInLayout() == 'coupons' || $block->getBlockAlias() == 'coupons')
                && ($request->getControllerName() == "order_create"
                        || $request->getControllerName() == "order_edit")){
			
			$extraBlock = $block->getLayout()->createBlock('J2t\Rewardpoints\Block\Adminhtml\Createorders\Reward');
			$extraBlock->setTemplate('form.phtml');
			$extraBlock->setNameInLayout("reward_coupons");
            $extraHtml    = $extraBlock->toHtml();
			echo $extraHtml;
			//TODO: check if module is active in order to show new block
			/*$transport          = $observer->getTransport();
			$fileName           = $block->getTemplateFile();
			$thisClass          = get_class($block);

			$html = $transport->getHtml();
			$magento_block = Mage::getSingleton('core/layout');
			$productsHtml = $magento_block->createBlock('rewardpoints/adminhtml_createorder_reward');
			$productsHtml->setTemplate('rewardpoints/form.phtml');
			$productsHtml->setNameInLayout("reward_coupons");
			$extraHtml    = $productsHtml->toHtml();
			$transport->setHtml($extraHtml.$html);*/
			
        }
        return $this;
    }
}
