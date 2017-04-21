<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Stats;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Block Module
     *
     * @var string
     */
    protected $_module = 'rewardpoints_admin';

    protected $moduleManager, $_setsFactory, $_flatPointFactory, $_type, $_status, $_visibility, $_websiteFactory;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \J2t\Rewardpoints\Model\FlatpointFactory $flatPointFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_websiteFactory = $websiteFactory;
        $this->_setsFactory = $setsFactory;
        $this->_flatPointFactory = $flatPointFactory;
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
        $this->setId('statsGrid');
        $this->setDefaultSort('user_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
        $this->setUseAjax(false);
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
        $collection = $this->_flatPointFactory->create()->getCollection()->addClientEntries()
        ->showCustomerInfo()
        /*->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'attribute_set_id'
        )->addAttributeToSelect(
            'type_id'
        )->setStore(
            $store
        )*/;
        $collection->getSelect()->group(
                'main_table.flat_account_id'
        );

        $this->setCollection($collection);

        parent::_prepareCollection();
        //$this->getCollection()->addWebsiteNamesToResult();
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
                'index' => 'flat_account_id',
                'filter_index' =>'main_table.flat_account_id',
                'header_css_class' => 'col-attr-code',
                'column_css_class' => 'col-attr-code'
            ]
        );

        $this->addColumn(
            'customer_firstname',
            [
                'header' => __('Customer First Name'),
                'sortable' => true,
                'index' => 'customer_firstname',
                'filter_index' =>'customer_firstname_table.value',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'customer_lastname',
            [
                'header' => __('Customer Last Name'),
                'sortable' => true,
                'index' => 'customer_lastname',
                'filter_index' =>'customer_lastname_table.value',
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
            'points_collected',
            [
                'header' => __('Accumulated points'),
                'sortable' => true,
                'index' => 'points_collected',
                'width'     => '50px',
                'filter'    => false,
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'points_used',
            [
                'header' => __('Spent points'),
                'sortable' => true,
                'index' => 'points_used',
                'width'     => '50px',
                'filter'    => false,
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'points_current',
            [
                'header' => __('Available points'),
                'sortable' => true,
                'index' => 'points_current',
                'width'     => '50px',
                'filter'    => false,
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'points_lost',
            [
                'header' => __('Lost'),
                'sortable' => true,
                'index' => 'points_lost',
                'width'     => '50px',
                'filter'    => false,
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
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
        );

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
}
