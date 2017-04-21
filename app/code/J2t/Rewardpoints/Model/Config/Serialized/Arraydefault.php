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

namespace J2t\Rewardpoints\Model\Config\Serialized;

use \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class Arraydefault extends ArraySerialized {

    /**
     * Design package instance
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design = null;
    protected $_localeDate;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\App\Config\ScopeConfigInterface $config, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\View\DesignInterface $design, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
    //\Magento\Framework\Data\Collection\Db $resourceCollection = null,
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
    ) {
        $this->_design = $design;
        $this->_localeDate = $localeDate;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Validate value
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     * if there is no field value, search value is empty or regular expression is not valid
     */
    public function beforeSave() {

        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $arr = array();
        foreach ($value as $key => $val) {
            /* echo "$key = $value
              "; */
            if (isset($val['min_value']) && isset($val['max_value']) && isset($val['duration'])) {
                $arr[$key] = $val;
            }
        }
        if (!$this->_checkOverlap($arr)) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Overlap issues. Please verify Customer Point Notifications values."));
        }
        $this->setValue($arr);
        return parent::beforeSave();
    }

    protected function _checkOverlap($array) {
        foreach ($array as $key => $value) {
            foreach ($array as $key_2 => $value_2) {
                if ($key != $key_2) {
                    if (($value['min_value'] >= $value_2['min_value']) && ($value['min_value'] <= $value_2['max_value'])) {
                        return false;
                    } else if (($value['max_value'] >= $value_2['min_value']) && ($value['max_value'] <= $value_2['max_value'])) {
                        return false;
                    }
                }
                if ($value['min_value'] >= $value['max_value']) {
                    return false;
                }
                if ($value['min_value'] <= 0 || $value['max_value'] <= 0) {
                    return false;
                }
            }
        }
        return true;
    }

}
