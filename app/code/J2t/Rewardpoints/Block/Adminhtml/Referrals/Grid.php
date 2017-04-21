<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Referrals;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Block Module
     *
     * @var string
     */
    protected $_module = 'rewardpoints_admin';

    protected $moduleManager, $_setsFactory, $_referralFactory, $_type, $_status, $_visibility, $_websiteFactory;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \J2t\Rewardpoints\Model\ReferralFactory $referralFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_websiteFactory = $websiteFactory;
        $this->_setsFactory = $setsFactory;
        $this->_referralFactory = $referralFactory;
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
        $this->setId('referralsGrid');
        $this->setDefaultSort('rewardpoints_referral_id');
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
        $collection = $this->_referralFactory->create()->getCollection();
                //->addClientEntries();

        $this->setCollection($collection);

        parent::_prepareCollection();
        
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
                'index' => 'rewardpoints_referral_id',
                'header_css_class' => 'col-attr-code',
                'column_css_class' => 'col-attr-code'
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Parent Email'),
                'sortable' => true,
                'index' => 'email',
                'width'     => '50px',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'rewardpoints_referral_email',
            [
                'header' => __('Referred Email'),
                'sortable' => true,
                'index' => 'rewardpoints_referral_email',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'rewardpoints_referral_name',
            [
                'header' => __('Referred Name'),
                'sortable' => true,
                'index' => 'rewardpoints_referral_name',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->addColumn(
            'rewardpoints_referral_status',
            [
                'header' => __('Status'),
                'sortable' => true,
                'index' => 'rewardpoints_referral_status',
                'width'     => '50px',
                'filter'    => false,
                'type'      => 'options',
                'options'   => ['1' => __('Has ordered'), '0' => __('Waiting for order')],
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
