<?php

namespace J2t\Rewardpoints\Controller\Index;

abstract class Index extends \Magento\Framework\App\Action\Action {
    public function excecute() {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}