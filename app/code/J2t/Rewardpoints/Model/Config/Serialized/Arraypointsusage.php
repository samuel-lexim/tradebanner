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

class Arraypointsusage extends ArraySerialized
{
    /**
     * Design package instance
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design = null;
	protected $_localeDate;
	protected $messageManager;

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
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        //\Magento\Framework\Data\Collection\Db $resourceCollection = null,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_design = $design;
		$this->_localeDate = $localeDate;
		$this->messageManager = $messageManager;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

	
	protected function _afterLoad()
    {
		$value = $this->getValue();
		$value = unserialize($value);
		if (is_array($value) && count($value)){
			foreach($value as $key => &$val){
				if (isset($val['date_from']) && $val['date_from'] != ''){
					$val['date_from'] = $this->_localeDate->formatDate($val['date_from'], \IntlDateFormatter::SHORT, false);
				}
				if (isset($val['date_end']) && $val['date_end'] != ''){
					$val['date_end'] = $this->_localeDate->formatDate($val['date_end'], \IntlDateFormatter::SHORT, false);
				}
			}
		}
		
		$value = serialize($value);
		$this->setValue($value);
		parent::_afterLoad();
    }
	
	
    /**
     * Validate value
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     * if there is no field value, search value is empty or regular expression is not valid
     */
    public function beforeSave()
    {
		
        $value = $this->getValue();
		

        if (is_array($value)) {
            unset($value['__empty']);
        }
		
		
		//echo "<pre>";
		//print_r($value);
		

        $arr = array();
        foreach($value as $key => $val){
            if (isset($val['min_cart_value']) && isset($val['max_cart_value']) && isset($val['point_value']) && isset($val['group_id'])
                    && isset($val['date_from']) && isset($val['date_end'])){

                if (is_float($val['point_value']) && (int)$val['point_value'] != 0){
					throw new \Magento\Framework\Exception\LocalizedException(__("Custom point values issue. Point value must be an integer."));
                    //Mage::getSingleton('adminhtml/session')->addError('Custom point values issue. Point value must be an integer.');
                }
				
                $fromDate = $toDate = null;
                $data = $this->_filterDates($val, array('date_from', 'date_end'));

                $fromDate = $data['date_from'];
                $toDate = $data['date_end'];
                if ($fromDate && $toDate) {
                    //$fromDate = new Zend_Date($fromDate, \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT);
                    //$toDate = new Zend_Date($toDate, \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT);
					//$fromDate = new \Magento\Framework\Stdlib\DateTime\Date($fromDate, DateTime::DATE_INTERNAL_FORMAT);
					//$toDate = new \Magento\Framework\Stdlib\DateTime\Date($toDate, DateTime::DATE_INTERNAL_FORMAT);
					
					//$fromDate = new \Magento\Framework\Stdlib\DateTime\Date($fromDate, \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT);
					//$toDate = new \Magento\Framework\Stdlib\DateTime\Date($toDate, \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT);
					
					/*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$dateFormat = $objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface')
						->getDateFormat(\IntlDateFormatter::SHORT);
					$filterInput = new \Zend_Filter_LocalizedToNormalized(['date_format' => $dateFormat]);
					$filterInternal = new \Zend_Filter_NormalizedToLocalized(
						['date_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT]
					);
					$fromDate = $filterInternal->filter($filterInput->filter($fromDate));
					$toDate = $filterInternal->filter($filterInput->filter($toDate));
					*/
                    $fromDate = new \DateTime($fromDate);
					$toDate = new \DateTime($toDate);

					if ($fromDate > $toDate) {
						//Mage::getSingleton('adminhtml/session')->addError('End Date must follow Start Date.');
						$this->messageManager->addError(__('End Date must follow Start Date.'));
					}
                }

                $arr[$key] = $data;
            }
        }
		//die;

        $this->setValue($arr);
        return parent::beforeSave();
		
    }

    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
		$dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
		
		$filterInput = new \Zend_Filter_LocalizedToNormalized(['date_format' => $dateFormat]);
		$filterInternal = new \Zend_Filter_NormalizedToLocalized(
			['date_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT]
		);
		//$value = $filterInternal->filter($filterInput->filter($value));
		
		
        /*$filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => $dateFormat
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT
        ));*/

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }
}
