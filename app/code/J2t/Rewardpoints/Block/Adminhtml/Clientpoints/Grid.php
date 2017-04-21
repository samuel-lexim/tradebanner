<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Clientpoints;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Block Module
     *
     * @var string
     */
    protected $_module = 'rewardpoints_admin';

    protected $moduleManager, $_setsFactory, $_pointFactory, $_type, $_status, $_visibility, $_websiteFactory;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \J2t\Rewardpoints\Model\PointFactory $pointFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_websiteFactory = $websiteFactory;
        $this->_setsFactory = $setsFactory;
        $this->_pointFactory = $pointFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
    
    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pointsGrid');
        $this->setDefaultSort('customer_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        //$this->setUseAjax(true);
        $this->setVarNameFilter('rewardpoint_filter');
    }
    
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
    
    
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = $this->_pointFactory->create()->getCollection()
                ->addClientEntries();

        $this->setCollection($collection);

        parent::_prepareCollection();
        //$this->getCollection()->addWebsiteNamesToResult();
        
        //TODO: modify the following
        /*if (!Mage::app()->isSingleStoreMode()) {
            $this->getCollection()->addStoreData();
        }*/
        
        return $this;
    } 

    /**
     * Prepare default grid column
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'rewardpoints_account_id',
                'header_css_class' => 'col-attr-code',
                'column_css_class' => 'col-attr-code'
            ]
        );

        $this->addColumn(
            'client_id',
            [
                'header' => __('Customer ID'),
                'sortable' => true,
                'index' => 'customer_id',
                'width'     => '50px',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'email',
            [
                'header' => __('Customer Email'),
                'sortable' => true,
                'index' => 'email',
                'filter_index' =>'cust.email',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'order_id',
            [
                'header' => __('Order ID'),
                'sortable' => true,
                'index' => 'order_id',
                'width'     => '50px',
                'filter'    => false,
                'renderer' => 'J2t\Rewardpoints\Block\Adminhtml\Grid\Column\Renderer\Order',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'order_id_corres',
            [
                'header' => __('Points type'),
                'sortable' => true,
                'index' => 'order_id',
                'width'     => '50px',
                'filter'    => false,
                'sortable'    => false,
                /*'renderer' => new Rewardpoints_Block_Adminhtml_Renderer_Pointstype(),*/
                'renderer' => 'J2t\Rewardpoints\Block\Adminhtml\Grid\Column\Renderer\PointsType',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        /*
         * TODO
         * if (Mage::getConfig()->getModuleConfig('J2t_Rewardsocial')->is('active', 'true')){
          $this->addColumn('rewardpoints_linker', array(
            'header'    => Mage::helper('rewardpoints')->__('Relation'),
            'align'     => 'right',
            'index'     => 'rewardpoints_linker',
            'width'     => '150px',
            'renderer' => new Rewardpoints_Block_Adminhtml_Renderer_Pointslink(),
            'filter'    => false,
            'sortable'  => false,
        ));
      }
         */
        
        
        $this->addColumn(
            'points_current',
            [
                'header' => __('Accumulated points'),
                'sortable' => true,
                'index' => 'points_current',
                'width'     => '50px',
                'filter'    => false,
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'points_spent',
            [
                'header' => __('Spent points'),
                'sortable' => true,
                'index' => 'points_spent',
                'width'     => '50px',
                'filter'    => false,
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        /* 
         * TODO
         * if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('rewardpoints')->__('Stores'),
                'index'     => 'stores',
                'type'      => 'store',
                'store_view' => false,
                'sortable'   => false,
            ));
        }
         */
        
        /*$this->addColumn(
            'last_check',
            [
                'header' => __('Calculation date'),
                'sortable' => true,
                'index' => 'last_check',
                'width'     => '50px',
                'type'    => 'date',
                'filter_index'      => 'main_table.last_check',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );*/

        return $this;
    }

    /**
     * Return url of given row
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl($this->_module . '/*/edit', ['id' => $row->getId()]);
    }
    
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }
}
