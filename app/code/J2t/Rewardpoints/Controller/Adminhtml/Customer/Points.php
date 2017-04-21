<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Adminhtml\Customer;

use Magento\Customer\Controller\RegistryConstants;

class Points extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer point's grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        //$this->_initCustomer();
        $customerId = $this->initCurrentCustomer();
        //$customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        /** @var  \Magento\Newsletter\Model\Subscriber $subscriber */
        /*$subscriber = $this->_objectManager
            ->create('Magento\Newsletter\Model\Subscriber')
            ->loadByCustomerId($customerId);

        $this->_coreRegistry->register('subscriber', $subscriber);*/
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
