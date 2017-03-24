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

class Calculationtype implements \Magento\Framework\Option\ArrayInterface
{
    const STATIC_VALUE = 0;
    const RATIO_POINTS = 1;
    const CART_SUMMARY = 2;
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        
        return [['value' => self::STATIC_VALUE, 'label' => __('Static value')], ['value' => self::RATIO_POINTS, 'label' => __('Ratio points')]
            , ['value' => self::CART_SUMMARY, 'label' => __('Cart summary Ratio points')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::STATIC_VALUE => __('Static value'), self::RATIO_POINTS => __('Ratio points'), self::CART_SUMMARY => __('Cart summary Ratio points')];
    }
}
