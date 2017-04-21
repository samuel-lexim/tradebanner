<?php
/**
 * Copyright © 2016 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Adminhtml\Dashboard\Tab;

class Gather extends \J2t\Rewardpoints\Block\Adminhtml\Dashboard\Graph
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Dashboard\Data $dashboardData
     * @param \Magento\Backend\Helper\Dashboard\Order $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \J2t\Rewardpoints\Model\Resource\Point\CollectionFactory $pointsCollectionFactory,
        \Magento\Backend\Helper\Dashboard\Data $dashboardData,
        //\Magento\Backend\Helper\Dashboard\Order $dataHelper,
        \J2t\Rewardpoints\Helper\Dashboard\Stats $dataHelper,
        array $data = []
    ) {
        //$this->setDataHelperName('rewardpoints/dashboard_stats');
        //\J2t\Rewardpoints\Helper\Dashboard\Stats
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $collectionFactory, $pointsCollectionFactory, $dashboardData, $data);
    }

    /**
     * Initialize object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setHtmlId('gather_points');
        parent::_construct();
    }

    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
        $this->getDataHelper()->setParam('website', $this->getRequest()->getParam('website'));
        $this->getDataHelper()->setParam('group', $this->getRequest()->getParam('group'));

        $this->setDataRows('points_current');
        $this->_axisMaps = ['x' => 'range', 'y' => 'points_current'];

        parent::_prepareData();
    }
}
