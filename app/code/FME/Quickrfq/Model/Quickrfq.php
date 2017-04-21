<?php

namespace FME\Quickrfq\Model;

class Quickrfq extends \Magento\Framework\Model\AbstractModel
{
        
        
    protected function _construct()
    {
        $this->_init('FME\Quickrfq\Model\ResourceModel\Quickrfq');
    }
        
        
    public function getAvailableStatuses()
    {
                
                
        $availableOptions = ['New' => 'New',
                          'Under Process' => 'Under Process',
                          'Pending' => 'Pending',
                          'Done' => 'Done'];
                
        return $availableOptions;
    }
        
    public function getBudgetStatuses()
    {
                
                
        $options = ['Approved' => 'Approved',
                          'Approval Pending' => 'Approval Pending',
                          'Open' => 'Open',
                          'No Approval' => 'No Approval'];
                
        return $options;
    }
}
