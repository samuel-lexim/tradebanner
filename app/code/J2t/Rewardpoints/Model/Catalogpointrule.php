<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model;

use Magento\Catalog\Model\Product;

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
class Catalogpointrule extends \Magento\Rule\Model\AbstractModel {

    const RULE_TYPE_CART = 1;
    const RULE_TYPE_DATAFLOW = 2;
    const RULE_ACTION_TYPE_ADD = 1;
    const RULE_ACTION_TYPE_DONTPROCESS = 2;
    const RULE_ACTION_TYPE_MULTIPLY = -1;
    const RULE_ACTION_TYPE_DIVIDE = -2;

    protected $_eventObject = 'catalogpointrule';
    protected $_productIds;
    protected $_productsFilter = null;
    protected $_now;
    protected static $_priceRulesData = [];
    protected $_catalogRuleData;
    protected $_cacheTypesList;
    protected $_relatedCacheTypes;
    protected $_resourceIterator;
    protected $_combineFactory;
    protected $_actionCollectionFactory;
    protected $_productFactory;
    protected $_productCollectionFactory;
    protected $dateTime;
    protected $_ruleProductProcessor;
    protected $_types;
    protected $_action_types;
    protected $storeManager;
    protected $_customerSession;
    protected $_condCombineFactory;

    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \J2t\Rewardpoints\Model\CatalogRule\Condition\CombineFactory $combineFactory, \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator, \Magento\Customer\Model\Session $customerSession, \Magento\CatalogRule\Helper\Data $catalogRuleData, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Framework\App\Cache\TypeListInterface $cacheTypesList, \Magento\Framework\Stdlib\DateTime $dateTime, \Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor $ruleProductProcessor,
    //\Magento\Framework\View\Element\Template\Context $viewContext,
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $relatedCacheTypes = [], array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        $this->_productFactory = $productFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_customerSession = $customerSession;
        $this->_catalogRuleData = $catalogRuleData;
        $this->_cacheTypesList = $cacheTypesList;
        $this->_relatedCacheTypes = $relatedCacheTypes;
        $this->dateTime = $dateTime;
        $this->_ruleProductProcessor = $ruleProductProcessor;

        $this->_pointData = $pointHelper;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    public function __construct_old(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory,
    //\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    //\Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory, \J2t\Rewardpoints\Model\CatalogRule\Condition\CombineFactory $condCombineFactoryJ2t, \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator, \Magento\Customer\Model\Session $customerSession, \Magento\CatalogRule\Helper\Data $catalogRuleData, \Magento\Framework\App\Cache\TypeListInterface $cacheTypesList, \Magento\Framework\Stdlib\DateTime $dateTime, \Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor $ruleProductProcessor, \Magento\Framework\View\Element\Template\Context $viewContext, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $relatedCacheTypes = [], array $data = []
    ) {
        $localeDate = $context->getLocaleDate();
        $storeManager = $viewContext->getStoreManager();
        //$this->_condCombineFactory = $condCombineFactoryJ2t;
        $this->_combineFactory = $condCombineFactoryJ2t;
        parent::__construct($context, $registry, $formFactory, $localeDate, $productCollectionFactory, $storeManager, $combineFactory, $actionCollectionFactory, $productFactory, $resourceIterator, $customerSession, $catalogRuleData, $cacheTypesList, $dateTime, $ruleProductProcessor, $resource, $resourceCollection, $relatedCacheTypes, $data);
        $this->_pointData = $pointHelper;
        $this->storeManager = $viewContext->getStoreManager();
        $this->_customerSession = $customerSession;
    }

    /* public function __construct(
      \Magento\Framework\Model\Context $context,
      \Magento\Framework\View\Element\Template\Context $viewContext,
      \Magento\Framework\Registry $registry,
      \J2t\Rewardpoints\Helper\Data $pointHelper,
      \Magento\Customer\Model\Session $customerSession,
      \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
      \Magento\Framework\Data\Collection\Db $resourceCollection = null,
      array $data = []
      ) {
      parent::__construct($context, $registry, $resource, $resourceCollection, $data);
      $this->_pointData = $pointHelper;
      $this->storeManager = $viewContext->getStoreManager();
      $this->_customerSession = $customerSession;
      } */

    protected function _construct() {
        parent::_construct();
        $this->_init('J2t\Rewardpoints\Model\Resource\Catalogpointrule');
        $this->setIdFieldName('rule_id');
        $this->_types = array(
            self::RULE_TYPE_CART => __('Cart rule'),
            self::RULE_TYPE_DATAFLOW => __('Import rule'),
        );
        $this->_action_types = array(
            self::RULE_ACTION_TYPE_ADD => __('Add / remove points'),
            self::RULE_ACTION_TYPE_DONTPROCESS => __("Don't gather points"),
            self::RULE_ACTION_TYPE_MULTIPLY => __("Multiply By"),
            self::RULE_ACTION_TYPE_DIVIDE => __("Divide By"),
        );
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

    public function getCustomerGroupIds() {
        return $this->_getData('customer_group_ids');
    }

    public function getNow() {
        if (!$this->_now) {
            return (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        return $this->_now;
    }

    public function setNow($now) {
        $this->_now = $now;
    }

    /* public function validateData(\Magento\Framework\DataObject $dataObject)
      {
      $result = parent::validateData($dataObject);
      if ($result === true) {
      $result = [];
      }

      $action = $dataObject->getData('simple_action');
      $discount = $dataObject->getData('discount_amount');
      $result = array_merge($result, $this->validateDiscount($action, $discount));
      if ($dataObject->getData('sub_is_enable') == 1) {
      $action = $dataObject->getData('sub_simple_action');
      $discount = $dataObject->getData('sub_discount_amount');
      $result = array_merge($result, $this->validateDiscount($action, $discount));
      }

      return !empty($result) ? $result : true;
      } */

    public function setProductsFilter($productIds) {
        $this->_productsFilter = $productIds;
    }

    public function getProductsFilter() {
        return $this->_productsFilter;
    }

    protected function dataDiff($array1, $array2) {
        $result = [];
        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                if (is_array($value)) {
                    if ($value != $array2[$key]) {
                        $result[$key] = true;
                    }
                } else {
                    if ($value != $array2[$key]) {
                        $result[$key] = true;
                    }
                }
            } else {
                $result[$key] = true;
            }
        }
        return $result;
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

    public function getConditionsInstance() {
        return $this->_combineFactory->create();
        //TODO: check how magento does
        //return Mage::getModel('rewardpoints/catalogpointrule_condition_combine');
    }

    public function getActionsInstance() {
        return $this->_actionCollectionFactory->create();
    }

    public function checkRule($to_validate) {

        $storeId = $this->storeManager->getStore()->getId();
        ;
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        if ($to_validate->getCustomerGroupId()) {
            $customerGroupId = $to_validate->getCustomerGroupId();
        } else {
            $customerGroupId = $this->_customerSession->getCustomerGroupId();
        }

        /*
         * TODO
          $rules = Mage::getModel('rewardpoints/catalogpointrules')->getCollection()->setValidationFilter($websiteId, $customerGroupId, $couponCode);

          foreach($rules as $rule)
          {
          if (!$rule->getStatus()) continue;
          $rule_validate = Mage::getModel('rewardpoints/catalogpointrules')->load($rule->getRuleId());

          if ($rule_validate->validate($to_validate)){
          //Rule OK
          Mage::getModel('rewardpoints/subscriptions')->updateSegments($to_validate->getEmail(), $rule);
          } else {
          //Rule KO
          Mage::getModel('rewardpoints/subscriptions')->unsubscribe($to_validate->getEmail(), $rule);

          }
          } */
    }

    /* TODO
      public function getPointrulesByIds($ids)
      {
      $segmentsids = explode(',', $ids);
      $segmentstitles = array();
      foreach ($segmentsids as $segmentid)
      {
      $collection = $this->getCollection();
      $collection->getSelect()
      ->where('rule_id = ?', $segmentid);
      $row = $collection->getFirstItem();
      $segmentstitles[] = $row->getTitle();
      }
      return implode(',', $segmentstitles);
      }

      public function getSegmentsRule()
      {
      $segments = array();
      $collection = $this->getCollection();
      $collection->getSelect()
      ->order('title');
      $collection->load();

      foreach ($collection as $key=>$values)
      {
      $segments[]=array('label'=>$values->getTitle() ,'value'=>$values->getRuleId());
      }
      return $segments;
      }

      public function getCatalogPointsByCart(){
      $points = 0;
      $_cart_products = Mage::getModel("checkout/cart")->getItems();
      foreach($items as $item) {
      if($item->getProduct()->getId()) {
      //get product et cart quantity
      $product = Mage::getModel("catalog/product")->load($item->getProduct()->getId());
      //JON
      $item_default_points = $this->getItemPoints($item, Mage::app()->getStore()->getId());
      $points = getAllCatalogRulePointsGathered($product, $item_default_points);
      if ($points === false){
      return false;
      } elseif ($points > 0){
      $points = $points * $item->getQty();
      }
      }
      }
      return $points;
      }


      public function getAllCatalogRulePointsGathered($product = null, $item_default_points = null, $storeId = false, $default_qty = 1, $customerGroupId = null, $quote_item = null)
      {
      $points = $this->getCatalogRulePointsGathered($product, $item_default_points, $storeId, $default_qty, null, null, $quote_item);
      return $points;
      }

      public function getCatalogRulePointsJson($to_validate, $storeId = false, $default_qty = 1, $customerGroupId = null)
      {
      if (!$storeId){
      $storeId = Mage::app()->getStore()->getId();
      }

      $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
      if ($to_validate->getCustomerGroupId() && $customerGroupId == null){
      $customerGroupId = $to_validate->getCustomerGroupId();
      } else if ($customerGroupId == null){
      $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
      }

      $rules = Mage::getModel('rewardpoints/catalogpointrules')->getCollection()->setValidationFilter($websiteId, $customerGroupId);
      $return_val = array();
      foreach($rules as $rule)
      {
      if (!$rule->getStatus()) continue;
      $return_val[] = $rule->getData();
      }
      if (version_compare(Mage::getVersion(), '1.4.0', '>=')){
      return Mage::helper('core')->jsonEncode($return_val);
      } else {
      return Zend_Json::encode($return_val);
      }
      }

      public function validate(Varien_Object $object) {
      return parent::validate($object);
      }

      public function getCatalogRulePointsGathered($to_validate, $item_default_points = null, $storeId = false, $default_qty = 1, $customerGroupId = null, $onlyMultiplyDivide = false, $quote_item = null, $forceStop = false, $onlyAddRemove = false)
      {
      $points = 0;

      if (!$storeId){
      $storeId = Mage::app()->getStore()->getId();
      }



      $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
      if ($to_validate->getCustomerGroupId() && $customerGroupId == null){
      $customerGroupId = $to_validate->getCustomerGroupId();
      } else if ($customerGroupId == null){
      $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
      }

      $rules = Mage::getModel('rewardpoints/catalogpointrules')->getCollection()->setValidationFilter($websiteId, $customerGroupId);
      $points_temp = 0;

      $rules_message_cart = array();
      $cpt = 0;
      $multiply = 0;
      $divide = 0;

      $mulordiv = false;


      foreach($rules as $rule)
      {
      if (!$rule->getStatus()) continue;

      $rule_validate = Mage::getModel('rewardpoints/catalogpointrules')->load($rule->getRuleId());

      if ($rule_validate->validate($to_validate)){
      $cpt++;
      $message = "";
      if (($labels = $rule_validate->getLabelsSummary()) && $labels_array = unserialize($rule_validate->getLabelsSummary())){
      if(isset($labels_array[$storeId]) && trim($labels_array[$storeId]) != ""){
      $message = $labels_array[$storeId];
      }
      }

      if (($rule_validate->getActionType() == self::RULE_ACTION_TYPE_DONTPROCESS && !$onlyMultiplyDivide) || ( $rule_validate->getActionType() == self::RULE_ACTION_TYPE_DONTPROCESS && $forceStop )){
      return false;
      } else if ($rule_validate->getActionType() == self::RULE_ACTION_TYPE_MULTIPLY && !$onlyAddRemove){
      $multiply = ($rule_validate->getPoints() <= 0) ? 1 : $rule_validate->getPoints();
      $item_default_points = $item_default_points / $default_qty;
      $points_temp = ($item_default_points * $multiply);
      if ($mulordiv){
      $points += $points_temp;
      } else {
      $points += $points_temp - $item_default_points;
      }
      if ($message){
      $rules_message_cart[] = Mage::helper('rewardpoints')->__('points multiplied by %s, %s (%s points)', $rule_validate->getPoints(), $message, ceil($points_temp));
      } else {
      $rules_message_cart[] = Mage::helper('rewardpoints')->__('points multiplied by %s', $rule_validate->getPoints());
      }
      $mulordiv = true;
      } else if ($rule_validate->getActionType() == self::RULE_ACTION_TYPE_DIVIDE && !$onlyAddRemove){
      $divide = ($rule_validate->getPoints() <= 0) ? 1 : $rule_validate->getPoints();

      $item_default_points = $item_default_points / $default_qty;
      $points_temp = $item_default_points / $divide;

      if ($mulordiv){
      $points += $points_temp;
      } else {
      $points += $points_temp - $item_default_points;
      }

      if ($message){
      $rules_message_cart[] = Mage::helper('rewardpoints')->__('points divided by %s, %s (%s points)', $rule_validate->getPoints(), $message, ceil($points_temp));
      } else {
      $rules_message_cart[] = Mage::helper('rewardpoints')->__('points divided by %s', $rule_validate->getPoints());
      }
      $mulordiv = true;

      } else if ((!$onlyMultiplyDivide || $onlyAddRemove) && $rule_validate->getActionType() == self::RULE_ACTION_TYPE_ADD) {
      $points += $rule_validate->getPoints();

      if ($rule_validate->getPoints() > 0){
      if ($message){
      $rules_message_cart[] = Mage::helper('rewardpoints')->__("%s extra points added, %s", $rule_validate->getPoints(), $message);
      } else {
      $rules_message_cart[] = Mage::helper('rewardpoints')->__("%s extra points added", $rule_validate->getPoints());
      }
      } else {
      if ($message){
      $rules_message_cart[] = Mage::helper('rewardpoints')->__("%s points substracted, %s", $rule_validate->getPoints(), $message);
      } else {
      $rules_message_cart[] = Mage::helper('rewardpoints')->__("%s points substracted", $rule_validate->getPoints());
      }
      }
      }
      } else {

      }

      }
      if (sizeof($rules_message_cart) && is_object($to_validate) && $quote_item){
      //rewardpoints_catalog_rule_text
      $quote_item->setRewardpointsCatalogRuleText(serialize($rules_message_cart));
      } else if (sizeof($rules_message_cart) && is_object($to_validate) && get_class($to_validate) == "Mage_Catalog_Model_Product" && Mage::registry('current_product') && is_object(Mage::registry('current_product')) && Mage::registry('current_product')->getId()){
      $point_details = unserialize(Mage::registry('current_product')->getPointDetails());
      if ($point_details && is_array($point_details) && sizeof($point_details)){
      $point_details[$to_validate->getId()] = $rules_message_cart;
      Mage::registry('current_product')->setPointDetails(serialize($point_details));
      Mage::registry('current_product')->setPointRuleTotal($points+$item_default_points);
      } else {
      Mage::registry('current_product')->setPointDetails(serialize(array($to_validate->getId() => $rules_message_cart)));
      Mage::registry('current_product')->setPointRuleTotal($points+$item_default_points);
      }
      }
      return $points;
      }

      public function validateVarienData(Varien_Object $object)
      {
      if($object->getData('from_date') && $object->getData('to_date')){
      $dateStartUnixTime = strtotime($object->getData('from_date'));
      $dateEndUnixTime   = strtotime($object->getData('to_date'));

      if ($dateEndUnixTime < $dateStartUnixTime) {
      return array(Mage::helper('rule')->__("End Date should be greater than Start Date"));
      }
      }
      return true;
      } */
}
