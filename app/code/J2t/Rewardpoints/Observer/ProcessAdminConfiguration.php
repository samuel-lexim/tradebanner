<?php

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessAdminConfiguration implements ObserverInterface {
    
    protected $request;
    protected $_storeInterface;
    protected $_scopeConfig;
    protected $_moduleList;
    protected $curl;
    protected $messageManager;
    
    public function __construct($eventManager = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->request = $objectManager->get('Magento\Framework\App\Request\Http');
        $this->_storeInterface = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_scopeConfig = $objectManager->get('Magento\Framework\Config\Scope');
        $this->_moduleList = $objectManager->get('Magento\Framework\Module\ModuleList');
        
        $this->curl = $objectManager->get('Magento\Framework\HTTP\Adapter\Curl');
        
        $this->messageManager = $objectManager->get('Magento\Framework\Message\ManagerInterface');
        
        
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        return true;
        $event = $observer->getEvent();
        if ($this->request->getParam('section') == 'rewardpoints' &&
                $event->getData('config_data') &&
                ($groups = $event->getData('config_data')->getData('groups')) &&
                isset($groups['module_serial']) && isset($groups['module_serial']['fields']) &&
                isset($groups['module_serial']['fields']['key']) && isset($groups['module_serial']['fields']['key']['inherit']) &&
                $groups['module_serial']['fields']['key']['inherit'] == 1
        ) {
            $website = $this->request->getParam('website');
            $storeId = $this->_storeInterface->getStore()->getId();
            
            
            $module_key = $this->_storeInterface->getStore()->getConfig('rewardpoints/module_serial/key');
            

            $exceptions = array();
            $exceptions[] = __(base64_decode("U2VyaWFsIHVzZWQgaW4gaW52YWxpZCwgdGhlcmVmb3JlLCB5b3VyIGNvbmZpZ3VyYXRpb24gY2Fubm90IGJlIHNhdmVkLg=="));

            $moduleDetails = $this->_moduleList->getOne('J2t_Rewardpoints');
            $version = $moduleDetails['setup_version'];
            
            $version_array = explode('.', $version);
            $module_branch_version = $version_array[0] . '.' . $version_array[1];

            $ser_name_code = 'verser';
            $store_code = 'default';
            
            $store = $this->_storeInterface->getStore();
            
            if ($this->_storeInterface->getStore()->getCode()){
                $store_code = $this->_storeInterface->getStore()->getCode();
            }

            $url = parse_url($store->getBaseUrl());
            $domain = $url['host'];
            
            $url = "http://www." . base64_decode("ajJ0LWRlc2lnbi5uZXQ=") . "/j2tmoduleintegrity/index/checkIntegrityNew/version/$module_branch_version/serial/$module_key/code/rewardpoints/domain/$domain";
            
            $this->curl->setConfig(['timeout' => 20]);
            $this->curl->write('GET', $url);
            $data = $this->curl->read();
            
            

            $fs = false;
            if ($data === false || $this->curl->getErrno()) {
                $exceptions[] = _(base64_decode("Q1VSTCBlcnJvciAlcw=="), "(#{$this->curl->getErrno()}) / " . $this->curl->getError());
                $fs = true;
            } else {
                $exceptions[] = _(base64_decode("Tm8gQ1VSTCBhY2Nlc3MgZXJyb3Jz"));
            }
            $return_curl = preg_split('/^\r?$/m', $data, 2);
            $return_curl = trim($return_curl[1]);
            $this->curl->close();

            if ($return_curl === "" && $return_curl !== "0" && $return_curl !== "1" && !$fs) {
                $return_curl = 1;
            } elseif ($return_curl != "1") {

                $return_curl = 0;
            }

            if (!$return_curl) {
                throw new \RuntimeException("\n" . implode("\n", $exceptions));
            } else {
                $this->messageManager->addSuccess(_(base64_decode('W1tZb3VyIHNlcmlhbCBpcyB2YWxpZCBhbmQgY29uZmlndXJhdGlvbiBjYW4gYmUgc2F2ZWQuXV0=')));
            }
        }
    }

}
