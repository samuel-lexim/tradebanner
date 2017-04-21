<?php
/**
 * Copyright © 2016 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Adminhtml\Dashboard;

class Totals extends \Magento\Backend\Block\Dashboard\Bar
{
    /**
     * @var string
     */
    protected $_template = 'dashboard/totalbar.phtml';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    protected $_pointModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \J2t\Rewardpoints\Model\Resource\Point\CollectionFactory $rewardCollectionFactory,
        \J2t\Rewardpoints\Model\Point $pointModel,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $collectionFactory, $data);
        $this->_collectionFactory = $rewardCollectionFactory;
        $this->_pointModel = $pointModel;
    }

    /**
     * @return $this|void
     */
    protected function _prepareLayout()
    {
        if (!$this->_moduleManager->isEnabled('Magento_Reports')) {
            return $this;
        }
        $isFilter = $this->getRequest()->getParam(
            'store'
        ) || $this->getRequest()->getParam(
            'website'
        ) || $this->getRequest()->getParam(
            'group'
        );
        $period = $this->getRequest()->getParam('period', '24h');

        /* @var $collection \Magento\Reports\Model\ResourceModel\Order\Collection */
        $collection = $this->_collectionFactory->create()->addCreateAtPeriodFilter(
            $period
        )->calculateTotals(
            false
        );
        
        $collectionOther = $this->_collectionFactory->create()->addCreateAtPeriodFilter(
            $period
        )->calculateTotals(
            true
        );

        if ($this->getRequest()->getParam('store')) {
            //$collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
            $findset = array('finset' => array($this->getParam('store')));
            $collection->addFieldToFilter('store_id', $findset);
            $collectionOther->addFieldToFilter('store_id', $findset);
        } else {
            if ($this->getRequest()->getParam('website')) {
                $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
                //$collection->addFieldToFilter('store_id', ['in' => $storeIds]);
                echo 'ici';
                foreach ($storeIds as $storeId){
                    $findset = array('finset' => array($storeId));
                    $collection->addFieldToFilter('store_id', $findset);
                    $collectionOther->addFieldToFilter('store_id', $findset);
		}
            } else {
                if ($this->getRequest()->getParam('group')) {
                    echo 'la';
                    $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
                    //$collection->addFieldToFilter('store_id', ['in' => $storeIds]);
                    foreach ($storeIds as $storeId){
                        $findset = array('finset' => array($storeId));
                        $collection->addFieldToFilter('store_id', $findset);
                        $collectionOther->addFieldToFilter('store_id', $findset);
                    }
                } else { //if (!$collection->isLive()) {
                    
                    /*$collection->addFieldToFilter(
                        'store_id',
                        ['eq' => $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()]
                    );*/
                    if ($this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()){
                        $findset = ['finset' => [$this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()]];
                        $collection->addFieldToFilter('store_id', $findset);
                        $collectionOther->addFieldToFilter('store_id', $findset);
                    }
                    
                }
            }
        }

        //echo $collection->getSelect()->__toString();
        
        $collection->load();

        $totals = $collection->getFirstItem();

        $this->addTotal(__('Accumulated Points'), $totals->getAllPointsGathered() + 0, true);
        $this->addTotal(__('Spent Points'), $totals->getAllPointsSpent() + 0, true);
        
        
        $arrayTypes = $this->_pointModel->getPointsDefaultTypeToArray();
        $loaded_collection = $collectionOther->load();
        
        $shown = array();
        if ($loaded_collection->getSize()){
            foreach ($loaded_collection as $pointType){
                if (isset($arrayTypes[$pointType->getOrderId()])){
                    $this->addTotal($arrayTypes[$pointType->getOrderId()], $pointType->getAllPointsGathered() + 0, true);
                    $shown[] = $pointType->getOrderId();
                }
            }
        }
        
        foreach ($arrayTypes as $key => $value){
            if (!in_array($key, $shown)){
                //specific point type
                //$this->addTotal($value, 0, true);
            }
        }

        /*$this->addTotal(__('Revenue'), $totals->getRevenue());
        $this->addTotal(__('Tax'), $totals->getTax());
        $this->addTotal(__('Shipping'), $totals->getShipping());
        $this->addTotal(__('Quantity'), $totals->getQuantity() * 1, true);*/
    }
}
