<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Quickrfq\Model\Config\Source;

class status implements \Magento\Framework\Option\ArrayInterface
{
   
    public function toOptionArray()
    {

        $options[] = ['value' => 'New','label' => 'New'];
          $options[] = ['value' =>  'Under Process' , 'label' => 'Under Process'];
           $options[] = ['value' => 'Pending' , 'label' => 'Pending'];
            $options[] =['value'=> 'Done','label' => 'Done'];
                
        return $options;
    }
}
