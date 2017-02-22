<?php

namespace FME\Quickrfq\Model\ResourceModel;

class Quickrfq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
        
        
           
    protected function _construct()
    {
        $this->_init('fme_quickrfq', 'quickrfq_id');
    }
}
