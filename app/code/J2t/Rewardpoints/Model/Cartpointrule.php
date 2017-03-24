<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model;

/**
 * Customer group model
 *
 * @method \Magento\Customer\Model\Resource\Group _getResource()
 * @method \Magento\Customer\Model\Resource\Group getResource()
 * @method string getCustomerGroupCode()
 * @method \Magento\Customer\Model\Group setCustomerGroupCode(string $value)
 * @method \Magento\Customer\Model\Group setTaxClassId(int $value)
 * @method Group setTaxClassName(string $value)
 */
class Cartpointrule extends \Magento\Rule\Model\AbstractModel {

    protected $_types;
    protected $_action_types;
    protected $storeManager;
    protected $_customerSession;

    const RULE_TYPE_CART = 1;
    const RULE_TYPE_DATAFLOW = 2;
    const RULE_ACTION_TYPE_ADD = 1;
    const RULE_ACTION_TYPE_DONTPROCESS = 2;
    const RULE_ACTION_TYPE_DONTPROCESS_USAGE = 3;
    const RULE_ACTION_TYPE_MULTIPLY = -1;
    const RULE_ACTION_TYPE_DIVIDE = -2;

    protected $_eventPrefix = 'rewardpoints_cartpointrule';
    protected $_eventObject = 'cartpointrule';
    protected $_validatedAddresses = [];
    protected $_condCombineFactory;
    protected $_condProdCombineF;
    protected $_storeManager;

    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \J2t\Rewardpoints\Model\Rule\Condition\CombineFactory $condCombineFactory, \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF, \Magento\Store\Model\StoreManagerInterface $storeManager, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
    ) {
        $this->_condCombineFactory = $condCombineFactory;
        $this->_condProdCombineF = $condProdCombineF;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);


        $this->_pointData = $pointHelper;
        $this->_customerSession = $customerSession;

        $this->_types = array(
            self::RULE_TYPE_CART => __('Cart rule'),
            self::RULE_TYPE_DATAFLOW => __('Import rule'),
        );
        $this->_action_types = array(
            self::RULE_ACTION_TYPE_ADD => __('Add / remove points'),
            self::RULE_ACTION_TYPE_DONTPROCESS => __("Don't gather points"),
            self::RULE_ACTION_TYPE_DONTPROCESS_USAGE => __("Forbid point usage"),
            self::RULE_ACTION_TYPE_MULTIPLY => __("Multiply By"),
            self::RULE_ACTION_TYPE_DIVIDE => __("Divide By"),
        );
    }

    protected function _construct() {
        parent::_construct();
        $this->_init('J2t\Rewardpoints\Model\Resource\Cartpointrule');
        $this->setIdFieldName('rule_id');
    }

    public function loadPost(array $data) {
        parent::loadPost($data);

        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    public function getConditionsInstance() {
        return $this->_condCombineFactory->create();
    }

    public function getActionsInstance() {
        return $this->_condProdCombineF->create();
    }

    public function ruletypesToOptionArray() {
        return $this->_toOptionArray($this->_types);
    }

    public function ruletypesToArray() {
        return $this->_toArray($this->_types);
    }

    public function ruleActionTypesToOptionArray() {
        return $this->_toOptionArray($this->_action_types);
    }

    public function ruleActionTypesToArray() {
        return $this->_toArray($this->_action_types);
    }

    protected function _toOptionArray($array) {
        $res = array();
        foreach ($array as $value => $label) {
            $res[] = ['value' => $value, 'label' => $label];
        }
        return $res;
    }

    protected function _toArray($array) {
        $res = array();
        foreach ($array as $value => $label) {
            $res[$value] = $label;
        }
        return $res;
    }

    public function getStoreLabels() {
        return unserialize($this->_getData('labels'));
    }

    public function hasIsValidForAddress($address) {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? true : false;
    }

    public function setIsValidForAddress($address, $validationResult) {
        $addressId = $this->_getAddressId($address);
        $this->_validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    public function getIsValidForAddress($address) {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? $this->_validatedAddresses[$addressId] : false;
    }

    private function _getAddressId($address) {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }

    public function getStoreSummaryLabels() {
        return unserialize($this->_getData('labels_summary'));
    }

    public function getCustomerGroupIds() {
        return $this->_getData('customer_group_ids');
    }

    public function beforeSave() {
        parent::beforeSave();


        if ($this->hasWebsiteIds() && is_array($this->getWebsiteIds())) {
            $this->setWebsiteIds(implode(',', $this->getWebsiteIds()));
        }

        if ($this->hasCustomerGroupIds() && is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(implode(',', $this->getCustomerGroupIds()));
        }

        return $this;
    }

}
