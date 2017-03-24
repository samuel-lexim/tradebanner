<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * System config image field backend model for Zend PDF generator
 *
 * @author     J2T Design Team <contact@j2t-design.net>
 */
namespace J2t\Rewardpoints\Model\Config\Source;

class Mathmethod implements \Magento\Framework\Option\ArrayInterface
{
    const MATH_DEFAULT	= 'default';
    const MATH_FLOOR	= 'floor';
    const MATH_ROUND	= 'round';
	const MATH_CEIL		= 'ceil';
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        
        return [['value' => self::MATH_DEFAULT, 'label' => __('Default')], ['value' => self::MATH_FLOOR, 'label' => __('Floor')]
            , ['value' => self::MATH_ROUND, 'label' => __('Round')], ['value' => self::MATH_CEIL, 'label' => __('Ceil')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::MATH_DEFAULT => __('Default'), self::MATH_FLOOR => __('Floor'), self::MATH_ROUND => __('Round'), self::MATH_CEIL => __('Ceil')];
    }
}
