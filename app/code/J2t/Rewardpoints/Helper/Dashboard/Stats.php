<?php
/**
 * Copyright © 2016 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Helper\Dashboard;

/**
 * Rewardpoints dashboard helper for points
 */
class Stats extends \Magento\Backend\Helper\Dashboard\AbstractDashboard
{
    /**
     * @var \Magento\Reports\Model\ResourceModel\Order\Collection
     */
    protected $_pointCollection;
    protected $_helper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Order\Collection $pointCollection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \J2t\Rewardpoints\Helper\Data $pointHelper,
        \J2t\Rewardpoints\Model\Resource\Point\Collection $pointCollection
    ) {
        $this->_pointCollection = $pointCollection;
        $this->_helper = $pointHelper;
        parent::__construct(
            $context
        );
    }

    /**
     * @return void
     */
    protected function _initCollection()
    {
        $isFilter = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');

        $this->_collection = $this->_pointCollection->prepareSummary($this->getParam('period'), 0, 0, $isFilter);

        if ($this->_helper->isApplyStoreScope()){
            if ($this->getParam('store')) {
                $this->_collection->addFieldToFilter('store_id', $this->getParam('store'));
            } elseif ($this->getParam('website')) {
                $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
                //$this->_collection->addFieldToFilter('store_id', ['in' => implode(',', $storeIds)]);
                foreach ($storeIds as $storeId){
                    $findset = array('finset' => array($storeId));
                    $this->_collection->addFieldToFilter('store_id', $findset);
                }
            } elseif ($this->getParam('group')) {
                $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
                //$this->_collection->addFieldToFilter('store_id', ['in' => implode(',', $storeIds)]);
                foreach ($storeIds as $storeId){
                    $findset = array('finset' => array($storeId));
                    $this->_collection->addFieldToFilter('store_id', $findset);
                } 
            } else { //if (!$this->_collection->isLive()) {
                /*$this->_collection->addFieldToFilter(
                    'store_id',
                    ['eq' => $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()]
                );*/
                
                $findset = array('finset' => array($this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()));
                $this->_collection->addFieldToFilter('store_id', $findset);
            }
        }
        $this->_collection->load();
    }
}
