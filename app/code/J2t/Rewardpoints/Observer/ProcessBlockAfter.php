<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessBlockAfter implements ObserverInterface {

    protected $_request;
    protected $_objectManager;
    protected $_url;

    /* public function __construct(
      \Magento\Framework\ObjectManager\ObjectManager $objectManager
      ) {
      $this->_objectManager = $objectManager;
      $this->_request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
      } */

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();

        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
        $this->_url = $this->_objectManager->get('Magento\Framework\UrlInterface');
        $this->_response = $this->_objectManager->get('Magento\Framework\App\Response\Http');
        $this->_actionFlag = $this->_objectManager->get('Magento\Framework\App\ActionFlag');
        
        if (strpos($this->_request->getPathInfo(), "referral-program") !== false) {
            $actionName = str_replace("referral-program", "", $this->_request->getPathInfo());

            $path_info = pathinfo($this->_request->getPathInfo());

            if (isset($path_info['filename'])) {
                $path = $path_info['filename'];
            } else {
                $path = substr($this->_request->getPathInfo(), 1, -1);
                $path = str_replace("referral-program/", "", $path);
            }

            $requestUri = $this->_url->getUrl('rewardpoints/referral/goReferral', ['_current' => true, 'decrypt' => $path]);
            $this->_response->setRedirect($requestUri);
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            return true;
        }
    }

}
