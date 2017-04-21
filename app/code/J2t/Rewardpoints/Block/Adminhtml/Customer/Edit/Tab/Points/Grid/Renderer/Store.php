<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Adminhtml\Customer\Edit\Tab\Points\Grid\Renderer;

/**
 * Adminhtml newsletter queue grid block status item renderer
 */
class Store extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeModel = null;
	public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\System\Store $store,
        array $data = []
    ) {
        $this->_storeModel = $store;
        parent::__construct($context, $data);
    }

    /**
     * Constructor for Grid Renderer Status
     *
     * @return void
     */
    /*protected function _construct()
    {
        self::$_statuses = [
            \Magento\Newsletter\Model\Queue::STATUS_SENT => __('Sent'),
            \Magento\Newsletter\Model\Queue::STATUS_CANCEL => __('Cancel'),
            \Magento\Newsletter\Model\Queue::STATUS_NEVER => __('Not Sent'),
            \Magento\Newsletter\Model\Queue::STATUS_SENDING => __('Sending'),
            \Magento\Newsletter\Model\Queue::STATUS_PAUSE => __('Paused'),
        ];
        parent::_construct();
    }*/

    /**
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
		$stores = explode(',', $row->getData($this->getColumn()->getIndex()));
        $storeName = array();
        if ($stores != array()){
            foreach ($stores as $storeId){
                //getStoreName
                $storeName[] = $this->_storeModel->getStoreName($storeId);
            }
        }

        return implode(', ', $storeName);
    }
}
