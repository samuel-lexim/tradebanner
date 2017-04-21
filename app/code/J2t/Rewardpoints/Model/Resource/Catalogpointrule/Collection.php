<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Resource\Catalogpointrule;

/**
 * Quotes collection
 *
 * @author      J2T Design Team <contact@j2t-design.net>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('J2t\Rewardpoints\Model\Catalogpointrule', 'J2t\Rewardpoints\Model\Resource\Catalogpointrule');
    }
    
    public function setValidationFilter($websiteId, $customerGroupId, $now=null)
    {
        if (!$this->getFlag('validation_filter')) {
            /* We need to overwrite joinLeft if coupon is applied */
			
			if (is_null($now)) {
				//$now = Mage::getModel('core/date')->date('Y-m-d');
				//$now = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d');
				$now = date('Y-m-d');
			}
			
            $this->getSelect()->reset();
            parent::_initSelect();

            $select = $this->getSelect();
            
            $select->where('status=1');
            $select->where('find_in_set(?, website_ids)', (int)$websiteId);
            $select->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);

            $select->where('from_date is null or from_date<=?', $now);
            $select->where('to_date is null or to_date>=?', $now);
            $select->order('sort_order');

            
            $this->setOrder('sort_order', self::SORT_ORDER_ASC);
            $this->setFlag('validation_filter', true);
        }

        return $this;
    }
    
    public function addWebsiteFilter($websiteIds)
    {
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }
        $parts = array();
        foreach ($websiteIds as $websiteId) {
            $parts[] = $this->getConnection()->quoteInto('FIND_IN_SET(?, main_table.website_ids)', $websiteId);
        }
        if ($parts) {
            $this->getSelect()->where(new Zend_Db_Expr(implode(' OR ', $parts)));
        }
        return $this;
    }
    
}
