<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Quickrfq\Model\Config\Source;

class budget implements \Magento\Framework\Option\ArrayInterface
{
   
    public function toOptionArray()
    {
        $options[] = ['value' => 'Approved','label' => 'Approved'];
          $options[] = ['value' =>  'Approval Pending' , 'label' => 'Approval Pending'];
           $options[] = ['value' => 'Open' , 'label' => 'Open'];
            $options[] =['value'=> 'No Approval','label' => 'No Approval'];
                
        return $options;
    }
}
