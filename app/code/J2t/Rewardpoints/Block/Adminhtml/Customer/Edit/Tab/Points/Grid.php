<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Adminhtml\Customer\Edit\Tab\Points;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml newsletter queue grid block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	
    protected $_module = 'rewardpoints_admin';
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Newsletter\Model\Resource\Queue\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        //\Magento\Newsletter\Model\Resource\Queue\CollectionFactory $collectionFactory,
		\J2t\Rewardpoints\Model\Resource\Point\CollectionFactory $collectionFactory,
		//\J2t\Rewardpoints\Model\PointFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pointsGrid');
        $this->setDefaultSort('rewardpoints_account_id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
        $this->setEmptyText(__('No Points Found'));
	}

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('rewardpoints_admin/customer/points', ['_current' => true]);
		//$this->getUrl('rewardpoints_admin/adminhtml_customerstats', array('_current'=>true));
		//rewardpoints_admin_customer_points
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        
        $collection = $this->_collectionFactory->create()->setUserFilter(
            $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'rewardpoints_account_id',
            ['header' => __('ID'), 'align' => 'left', 'index' => 'rewardpoints_account_id', 'width' => 10]
        );
		
		$this->addColumn('order_id', 
			[
            'header'    => __('Point Type'),
            'align'     => 'left',
            'index'     => 'order_id',
            'type'    => 'action',
            //'renderer' => new Rewardpoints_Block_Adminhtml_Renderer_Pointstype(),
				'renderer' => 'J2t\Rewardpoints\Block\Adminhtml\Customer\Edit\Tab\Points\Grid\Renderer\Pointstype',
            'filter'    => false,
            'sortable'  => false
			]
        );
		
		/*if (Mage::getConfig()->getModuleConfig('J2t_Rewardsocial')->is('active', 'true')){
            $this->addColumn('rewardpoints_linker', array(
              'header'    => Mage::helper('rewardpoints')->__('Relation'),
              'align'     => 'right',
              'index'     => 'rewardpoints_linker',
              'width'     => '150px',
              'renderer' => new Rewardpoints_Block_Adminhtml_Renderer_Pointslink(),
              'filter'    => false,
              'sortable'  => false,
          ));
        }*/

        $this->addColumn('points_current', 
			[
            'header'    => __('Accumulated Points'),
            'align'     => 'right',
            'index'     => 'points_current',
            'filter'    => false,
            'sortable'  => false
			]
        );
        $this->addColumn('points_spent',  
			[
            'header'    => __('Spent Points'),
            'align'     => 'right',
            'index'     => 'points_spent',
            'filter'    => false,
            'sortable'  => false
			]
        );
		
		if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_id', 
				[
                'header'    => __('Stores'),
                'align'     => 'left',
                'index'     => 'store_id',
                //'renderer' => new Rewardpoints_Block_Adminhtml_Renderer_Store(),
				'renderer' => 'J2t\Rewardpoints\Block\Adminhtml\Customer\Edit\Tab\Points\Grid\Renderer\Store',
                'sortable'  => false,
                'type'      => 'store'
				]
            );
        }
		
		$this->addColumn('date_start',  
			[
            'header'    => __('Start Date'),
            'index'     => 'date_start',
            'type'      => 'date',
            'width'     => '50px',
			'align' => 'center',
            'filter'    => false,
            'sortable'  => false
			]
        );


        $this->addColumn('date_end',  
			[
            'header'    => __('End Date'),
            'index'     => 'date_end',
            'type'      => 'date',
            'width'     => '50px',
			'align' => 'center',
            'filter'    => false,
            'sortable'  => false
			]
        );

        $this->addColumn('rewardpoints_referral_id',  
			[
            'header'    => __('Referred customer'),
            'align'     => 'left',
            'index'     => 'rewardpoints_referral_id',
            'type'    => 'action',
			'renderer' => 'J2t\Rewardpoints\Block\Adminhtml\Customer\Edit\Tab\Points\Grid\Renderer\Referral',
            'filter'    => false,
            'sortable'  => false
			]
        );
		/*
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'align' => 'center',
                'filter' => 'Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Filter\Status',
                'index' => 'queue_status',
                'renderer' => 'Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Renderer\Status'
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'align' => 'center',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Renderer\Action'
            ]
        );*/

        return parent::_prepareColumns();
    }
    
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }
}
