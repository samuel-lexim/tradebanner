<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Config\Backend;


class Serialverification extends \Magento\Framework\App\Config\Value
{
    /**
     * Core data
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    protected $_moduleList;
    protected $curl;
    
    protected $messageManager;
    protected $config;

    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Helper\Data $directoryHelper,
        \J2t\Rewardpoints\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->directoryHelper = $directoryHelper;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->_storeManager = $storeManager;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_moduleList = $objectManager->get('Magento\Framework\Module\ModuleList');
        $this->curl = $objectManager->get('Magento\Framework\HTTP\Adapter\Curl');
        $this->messageManager = $objectManager->get('Magento\Framework\Message\ManagerInterface');
        $this->config = $objectManager->get('Magento\Config\Model\ResourceModel\Config');
    }

    
    public function afterSave()
    {
        return true;
        $scope = $this->getScope();
        $scopeId = $this->getScopeId();
       
        $current_key = $this->getValue();

        $exceptions = array();
        $exceptions[] = __(base64_decode("U2VyaWFsIHVzZWQgaW4gaW52YWxpZCwgdGhlcmVmb3JlLCB5b3VyIGNvbmZpZ3VyYXRpb24gY2Fubm90IGJlIHNhdmVkLg=="));
        
        if (!empty($exceptions)) {
            $ser_name_code = 'verser';
            $store_code = 'default';

            if ($this->getStore()){
                $store_code = $this->getStore()->getCode();
                $store = $this->getStore();
            } else {
                $store_code = $this->_storeManager->getWebsite(
                                    $this->getWebsite()
                                )->getDefaultStore()->getCode();
                $store = $this->_storeManager->getWebsite(
                                    $this->getWebsite()
                                )->getDefaultStore();
            }
            
            $url = parse_url($store->getBaseUrl());
            $domain = $url['host'];
            
            if ($this->_helper->isSingleStoreModeEnabled() && ($scopeId != 0 || $scope != "default")){
                $this->config->saveConfig(
                    'rewardpoints/'.$ser_name_code.'/ok',
                    "0",
                    "default", 
                    0
                );
            }
            
            $this->config->saveConfig(
                    'rewardpoints/'.$ser_name_code.'/ok',
                    "0",
                    $scope, 
                    $scopeId
                );
            if (!$this->_helper->isSingleStoreModeEnabled()){
                foreach ($this->_storeManager->getStores() as $store_list){
                    if ($store_list->getCode() != $scope){
                        $this->config->saveConfig(
                            'rewardpoints/'.$ser_name_code.'/ok',
                            "0",
                            "stores", 
                            $store_list->getId()
                        );
                    }
                }
            }
            
            $this->_resetConfig();
            $moduleDetails = $this->_moduleList->getOne('J2t_Rewardpoints');
            $version = $moduleDetails['setup_version'];
            
            $version_array = explode('.', $version);
            $module_branch_version = $version_array[0].'.'.$version_array[1];
            $module_key = $current_key;

            $url = "http://www.".base64_decode("ajJ0LWRlc2lnbi5uZXQ=")."/j2tmoduleintegrity/index/checkIntegrityNew/version/$module_branch_version/serial/$module_key/code/rewardpoints/domain/$domain";

            
            $this->curl->setConfig(['timeout' => 20]);
            $this->curl->write('GET', $url);
            $data = $this->curl->read();
            
            $fs = false;
            if ($data === false || $this->curl->getErrno())
            {
                $exceptions[] = __(base64_decode("Q1VSTCBlcnJvciAlcw=="), "(#{$this->curl->getErrno()}) / ".$this->curl->getError());
                $fs = true;
            } else {
                $exceptions[] = __(base64_decode("Tm8gQ1VSTCBhY2Nlc3MgZXJyb3Jz"));
            }
            $return_curl = preg_split('/^\r?$/m', $data, 2);
            $return_curl = trim($return_curl[1]);
            $this->curl->close();
            if ($return_curl === "" && $return_curl !== "0" && $return_curl !== "1" && !$fs){
                $return_curl = 1;
            }
            
            if ($return_curl == 1){
                $this->config->saveConfig(
                    'rewardpoints/'.$ser_name_code.'/ok',
                    "1",
                    $scope, 
                    $scopeId
                    );
                
                if ($this->_helper->isSingleStoreModeEnabled() && ($scopeId != 0 || $scope != "default")){
                    $this->config->saveConfig(
                        'rewardpoints/'.$ser_name_code.'/ok',
                        "1",
                        "default", 
                        0
                    );
                }
                
                $this->_resetConfig();
            } else {
                $this->config->saveConfig(
                    'rewardpoints/'.$ser_name_code.'/ok',
                    "0",
                    $scope, 
                    $scopeId
                );
                
                if ($this->_helper->isSingleStoreModeEnabled() && ($scopeId != 0 || $scope != "default")){
                    $this->config->saveConfig(
                        'rewardpoints/'.$ser_name_code.'/ok',
                        "0",
                        "default", 
                        0
                    );
                }
                
                $this->_resetConfig();
            }
            
            $ok = $store->getConfig(
                'rewardpoints/'.$ser_name_code.'/ok'
            );

            if (!$ok){
                throw new \RuntimeException("\n" . implode("\n", $exceptions));
            } else {
                $this->messageManager->addSuccess(__(base64_decode('W1tZb3VyIHNlcmlhbCBpcyB2YWxpZCBhbmQgY29uZmlndXJhdGlvbiBjYW4gYmUgc2F2ZWQuXV0=')));
            }
        }
        return parent::afterSave();
        
    }
    
    protected function _resetConfig()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get('Magento\Framework\App\Config\ReinitableConfigInterface')->reinit();
        $objectManager->create('Magento\Store\Model\StoreManagerInterface')->reinitStores();
    }
    
}
