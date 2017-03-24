<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Adminhtml\Cartpointrules\Quote;

class NewAction extends \J2t\Rewardpoints\Controller\Adminhtml\Cartpointrules\Quote
{
    /**
     * New promo quote action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
