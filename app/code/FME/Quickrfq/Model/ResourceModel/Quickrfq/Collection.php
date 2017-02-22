<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Quickrfq\Model\ResourceModel\Quickrfq;

use FME\Quickrfq\Model\ResourceModel\AbstractCollection;

/**
 * CMS page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'quickrfq_id';

    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('FME\Quickrfq\Model\Quickrfq', 'FME\Quickrfq\Model\ResourceModel\Quickrfq');
        $this->_map['fields']['quickrfq_id'] = 'main_table.quickrfq_id';
    }

    
    public function addStoreFilter($store, $withAdmin = true)
    {
        return $this;
    }
}
