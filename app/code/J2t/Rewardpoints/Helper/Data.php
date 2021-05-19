<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile

namespace J2t\Rewardpoints\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Helper for coupon codes creating and managing
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * XML paths to reward points options
     */
    const XML_PATH_ACTIVE = 'rewardpoints/module_serial/active';
    const XML_PATH_SINGLE_STORE_MODE_ENABLED = 'general/single_store_mode/enabled';
    /* const XML_PATH_REFERRAL_SHOW                        = 'rewardpoints/registration/referral_show';

      const XML_PATH_DESIGN_SMALL_INLINE_IMAGE_SHOW       = 'rewardpoints/design/small_inline_image_show';
      const XML_PATH_DESIGN_SMALL_INLINE_IMAGE_SIZE       = 'rewardpoints/design/small_inline_image_size';
      const XML_PATH_POINT_MATH_GATHER                    = 'rewardpoints/default/math_method';
      const XML_PATH_POINT_MATH_USAGE                     = 'rewardpoints/default/math_method_usage';

      const XML_PATH_ADVANCED_REFERRAL_POINTS             = 'rewardpoints/referral_advanced/referral_steps';

      const XML_PATH_BUNDLE_CHILD                         = 'rewardpoints/default/bundle_rule_child';

      const XML_PATH_MAX_ORDER_POINTS                     = 'rewardpoints/default/max_point_percent_order'; */
    
    const XML_PATH_CUSTOM_POINT_VALUE                   = 'rewardpoints/point_custom/custom_point_value';
    const XML_PATH_CUSTOM_POINT_USAGE                   = 'rewardpoints/point_custom/custom_point_usage';
    
    const XML_PATH_NOTIFICATION_EMAIL_IDENTITY          = 'rewardpoints/notifications/notification_email_identity';
    const XML_PATH_NOTIFICATION_EMAIL_TEMPLATE          = 'rewardpoints/notifications/notification_email_template';
    
    const XML_PATH_NOTIFICATION_ADMIN_EMAIL_TEMPLATE    = 'rewardpoints/admin_notifications/notification_email_template';
    const XML_PATH_NOTIFICATION_ADMIN_EMAIL_IDENTITY    = 'rewardpoints/admin_notifications/notification_email_identity';
    
    const XML_PATH_CUSTOMER_NOTIFICATION_EMAIL_TEMPLATE = 'rewardpoints/status_notification/customer_point_status_email_template';
    
    
    const XML_PATH_MONEY_POINTS = 'rewardpoints/point_configuration/money_points';
    const XML_PATH_POINTS_MONEY = 'rewardpoints/point_configuration/points_money';
    const XML_PATH_MAX_COLLECTION = 'rewardpoints/point_configuration/max_point_collect_order';
    const XML_PATH_REGISTRATION_POINTS = 'rewardpoints/point_configuration/registration_points';
    const XML_PATH_PROCESS_TAX = 'rewardpoints/calculation_configuration/process_tax';
    const XML_PATH_EXCLUDE_TAX = 'rewardpoints/calculation_configuration/exclude_tax';
    const XML_PATH_REMOVE_DISCOUNT = 'rewardpoints/calculation_configuration/remove_discount';
    const XML_PATH_PROCESS_SHIPPING = 'rewardpoints/calculation_configuration/process_shipping';
    
    const XML_PATH_MATH_TOTAL_EARN = 'rewardpoints/calculation_configuration/math_total_earn';
    const XML_PATH_MATH_CATALOG_PAGES = 'rewardpoints/calculation_configuration/math_catalog_pages';
    const XML_PATH_SMALL_IMAGE = 'rewardpoints/design/small_inline_image';
    const XML_PATH_SMALL_IMAGE_SIZE = 'rewardpoints/design/small_inline_image_size';
    const XML_PATH_SMALL_IMAGE_SHOW = 'rewardpoints/design/small_inline_image_show';
    const XML_PATH_BIG_IMAGE = 'rewardpoints/design/big_inline_image';
    const XML_PATH_BIG_IMAGE_SIZE = 'rewardpoints/design/big_inline_image_size';
    const XML_PATH_BIG_IMAGE_SHOW = 'rewardpoints/design/big_inline_image_show';
    const XML_PATH_SHOW_VIEW = 'rewardpoints/display_settings/show_view_points';
    const XML_PATH_SHOW_LIST = 'rewardpoints/display_settings/show_list_points';
    const XML_PATH_POINT_EQUIVELENCE = 'rewardpoints/display_settings/point_equivalence';
    const XML_PATH_POINT_DETAILS = 'rewardpoints/display_settings/show_details';
    const XML_PATH_POINT_DASHBOARD = 'rewardpoints/display_settings/show_dashboard';
    
    const XML_PATH_USED_STATUSES = 'rewardpoints/points_validity/valid_used_statuses';
    const XML_PATH_VALID_STATUSES = 'rewardpoints/points_validity/valid_statuses';
    const XML_PATH_STATUS_FIELD = 'rewardpoints/points_validity/status_used';
    const XML_PATH_APPLY_STORE_SCOPE = 'rewardpoints/points_validity/store_scope';
    const XML_PATH_VALIDITY_DELAY = 'rewardpoints/points_validity/points_delay';
    const XML_PATH_VALIDITY_DURATION = 'rewardpoints/points_validity/points_duration';
    
    const XML_PATH_EVENTS_REVIEW = 'rewardpoints/events/review';
    const XML_PATH_EVENTS_NESWLETTER = 'rewardpoints/events/newsletter';
    const XML_PATH_EVENTS_POLL = 'rewardpoints/events/poll';
    
    
    const XML_PATH_REFERRAL_PERM = 'rewardpoints/referral/referral_permanent';
    const XML_PATH_REFERRAL_ADDTHIS = 'rewardpoints/referral/referral_addthis';
    const XML_PATH_REFERRAL_ADDTHIS_ACCOUNT = 'rewardpoints/referral/referral_addthis_account';
    const XML_PATH_REFERRAL_ADDTHIS_CODE = 'rewardpoints/referral/referral_addthis_code';
    const XML_PATH_REFERRAL_POINTS = 'rewardpoints/referral/referral_points';
    const XML_PATH_REFERRAL_CHILD_POINTS = 'rewardpoints/referral/referral_child_points';
    const XML_PATH_REFERRAL_REGISTRATION = 'rewardpoints/referral/referrer_registration_points';
    const XML_PATH_REFERRAL_CHILD_REGISTRATION = 'rewardpoints/referral/referred_registration_points';
    const XML_PATH_REFERRAL_POINT_METHOD = 'rewardpoints/referral/referral_points_method';
    const XML_PATH_REFERRAL_CHILD_POINT_METHOD = 'rewardpoints/referral/referral_child_points_method';
    const XML_PATH_REFERRAL_MIN_ORDER = 'rewardpoints/referral/referral_min_order';
    const XML_PATH_REFERRAL_CUSTOM_CODE = 'rewardpoints/referral/referral_custom_code';
    const XML_PATH_SUBSCRIPTION_EMAIL_TEMPLATE = 'rewardpoints/referral/subscription_email_template';
    const XML_PATH_SUBSCRIPTION_EMAIL_IDENTITY = 'rewardpoints/referral/subscription_email_identity';
    const XML_PATH_CONFIRMATION_EMAIL_TEMPLATE = 'rewardpoints/referral/confirmation_email_template';
    const XML_PATH_CONFIRMATION_EMAIL_IDENTITY = 'rewardpoints/referral/confirmation_email_identity';
    const XML_PATH_REFERRAL_REDIRECTION = 'rewardpoints/referral/referral_redirection';
    const XML_PATH_STEP_VALUE = 'rewardpoints/usage_specificities/step_value';
    const XML_PATH_STEP_MULTIPLIER = 'rewardpoints/usage_specificities/step_multiplier';
    const XML_PATH_STEP_SLIDER = 'rewardpoints/usage_specificities/step_slide';
    const XML_PATH_MIN_USE = 'rewardpoints/usage_specificities/min_use';
    const XML_PATH_MAX_POINT_USAGE = 'rewardpoints/usage_specificities/max_point_used_order';
    const XML_PATH_MAX_PERCENT_USAGE = 'rewardpoints/usage_specificities/max_point_percent_order';
    const XML_PATH_SHOW_REMOVE = 'rewardpoints/usage_specificities/show_remove_totals';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig, $_filesystem, $_storeManager, $_taxData, $_weeData, $coreData;
    protected $moduleManager;
    private $transportBuilder, $customerViewHelper, $customerRegistry, $_dateTime;
    protected $_catalogData = null;
    protected $_catalogPointRulesCollection = null;
    protected $_cartPointRulesCollection = null;
    protected $currentCustomer = null;
    private $session;
    private $rewardSession;
    protected $inlineTranslation;
    private $referralFactory;
    private $customerFactory;
    protected $_pointFactory;
    protected $_quoteFactory;
    
    protected $_appEmulation;
    protected $_transportBuilder;
    
    protected $_assetRepo;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $couponParameters
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context, 
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $taxData, 
        \Magento\Weee\Helper\Data $weeData, 
        \Magento\Framework\Pricing\Helper\Data $coreData,
        \Magento\Catalog\Helper\Data $catalogData, 
        \Magento\Framework\Stdlib\DateTime $dateTime, 
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, 
        \Magento\Customer\Helper\View $customerViewHelper, 
        \Magento\Customer\Model\CustomerRegistry $customerRegistry, 
        \J2t\Rewardpoints\Model\Resource\Catalogpointrule\CollectionFactory $catalogRules, 
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer, 
        \Magento\Customer\Model\Session $session,
        \J2t\Rewardpoints\Model\Session $rewardSession,
        \J2t\Rewardpoints\Model\Resource\Cartpointrule\CollectionFactory $cartRules,
        \J2t\Rewardpoints\Model\ReferralFactory $referralFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \J2t\Rewardpoints\Model\PointFactory $pointFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Quote\Model\QuoteFactory $quoteFactory, 
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, 
        Filesystem $filesystem
    ) {
        //$this->_scopeConfig = $scopeConfig;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_taxData = $taxData;
        $this->_weeData = $weeData;
        $this->coreData = $coreData;
        //$this->moduleManager = $moduleManager;
        $this->moduleManager = $context->getModuleManager();
        $this->_catalogData = $catalogData;
        $this->session = $session;
        $this->rewardSession = $rewardSession;
        $this->referralFactory = $referralFactory;
        $this->customerFactory = $customerFactory;
        $this->_pointFactory = $pointFactory;
        $this->_quoteFactory = $quoteFactory;
        $this->_assetRepo = $assetRepo;
        
        $this->_appEmulation = $appEmulation;
        $this->_transportBuilder = $transportBuilder;
        
        $this->inlineTranslation = $inlineTranslation;

        $this->_dateTime = $dateTime;
        $this->customerViewHelper = $customerViewHelper;
        $this->customerRegistry = $customerRegistry;
        $this->_catalogPointRulesCollection = $catalogRules;
        $this->_cartPointRulesCollection = $cartRules;
        $this->currentCustomer = $currentCustomer;

        parent::__construct($context);
    }
    
    protected function getCustomPointValue($cart, $default_value, $gather = true, $storeId = null) {
        if ($gather) {
            $custom_points = $this->_scopeConfig->getValue(self::XML_PATH_CUSTOM_POINT_VALUE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            $custom_points = $this->_scopeConfig->getValue(self::XML_PATH_CUSTOM_POINT_USAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        }
        
        $customer_group_id = $cart->getCustomerGroupId();
        $custom_points_array = unserialize($custom_points);
        $cart_amount = $cart->getShippingAddress()->getBaseSubtotal();
        
        
        if ($custom_points_array !== false && sizeof($custom_points_array)) {
            foreach ($custom_points_array as $custom_point) {
                if (isset($custom_point['min_cart_value']) && isset($custom_point['max_cart_value']) && isset($custom_point['point_value']) && isset($custom_point['group_id']) && isset($custom_point['date_from']) && isset($custom_point['date_end'])
                ) {
                    $nowDate = date("Y-m-d");
                    //$nowDate = new Zend_Date($nowDate, Varien_Date::DATE_INTERNAL_FORMAT);
                    $nowDate = new \DateTime($nowDate);
                    
                    if ($custom_point['date_from'] != "") {
                        $fromDate = new \DateTime($custom_point['date_from']);
                        //verify if now < fromDate
                        if ($nowDate < $fromDate) {
                            continue;
                        }
                    }
                    if ($custom_point['date_end'] != "") {
                        $endDate = new \DateTime($custom_point['date_end']);
                        if ($nowDate > $endDate) {
                            continue;
                        }
                    }
                    if ($custom_point['min_cart_value'] && $custom_point['min_cart_value'] > $cart_amount) {
                        continue;
                    }
                    if ($custom_point['max_cart_value'] && $custom_point['max_cart_value'] < $cart_amount) {
                        continue;
                    }
                    if (!in_array($customer_group_id, $custom_point['group_id'])) {
                        continue;
                    }
                    return $custom_point['point_value'];
                }
            }
        }
        return $default_value;
    }

    public function getStepValue($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_STEP_VALUE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStepMultiplier($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_STEP_MULTIPLIER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStepSlider($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_STEP_SLIDER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMinPointBalance($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_MIN_USE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function checkCustomerMinPoints($customerPoints, $storeId = null) {
        if ($customerPoints > $this->getMinPointBalance($storeId)) {
            return true;
        }
        return false;
    }

    public function getMaxPointUsage($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_MAX_POINT_USAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getShowRemoveLink($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_SHOW_REMOVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMaxPercentUsage($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_MAX_PERCENT_USAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMaxOrderUsage($cart, $points, $collectTotals = false, $storeId = null) {
        //check cart base subtotal
        if ($percent = $this->getMaxPercentUsage($storeId)) {
            //do collect totals
            $processTax = $this->getIncludeTax($storeId);
            $subtotalPrice = $cart->getShippingAddress()->getBaseSubtotal();
            if ($processTax){
                $subtotalPrice = $cart->getShippingAddress()->getBaseSubtotalInclTax();
            }
            
            if ($collectTotals && $subtotalPrice <= 0) {
                $cart->setByPassRewards(true);
                $cart->setTotalsCollectedFlag(false)->collectTotals();
                $cart->setByPassRewards(false);
                $subtotalPrice = $cart->getShippingAddress()->getBaseSubtotal();
                if ($processTax){
                    $subtotalPrice = $cart->getShippingAddress()->getBaseSubtotalInclTax();
                }
            }
            
            if ($subtotalPrice <= 0){
                foreach ($cart->getAllVisibleItems() as $item) {
                    if ($processTax){
                        $subtotalPrice += $item->getBasePriceInclTax()*$item->getQty();
                    } else {
                        $subtotalPrice += $item->getBasePrice()*$item->getQty();
                    }
                }
            }
            
            $baseSubtotalInPoints = $this->getPointsProductPriceEquivalence($subtotalPrice, $cart->getStoreId()) * $percent / 100;
            $points = min($points, $baseSubtotalInPoints);
        }
        if ($maxPointUsage = $this->getMaxPointUsage()) {
            $points = min($points, $maxPointUsage);
        }
        return $points;
    }

    public function cartRulesProcess($cart, $defaultPoints, $onlyUsage = false, $storeId = null, $groupId = null) {
        $storeId = ($storeId) ? $storeId : $this->_storeManager->getStore()->getId();
        $groupId = ($groupId) ? $groupId : $this->session->getCustomerGroupId();
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
        $collection = $this->_cartPointRulesCollection->create();
        $collection->setValidationFilter($websiteId, $groupId);
        if (!$onlyUsage) {
            $collection->addFieldToFilter('action_type', array('neq' => \J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_DONTPROCESS_USAGE));
        } else {
            $collection->addFieldToFilter('action_type', \J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_DONTPROCESS_USAGE);
        }
        $addedPoints = 0;
        //echo $collection->getSelect()->__toString();
        //die;

        $rules_message_cart = array();
        foreach ($collection as $rule) {
            if (!$rule->getStatus())
                continue;

            if ($rule->validate($cart)) {

                switch ($rule->getActionType()) {
                    case \J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_DONTPROCESS:
                        $cart->setRewardpointsCartRuleText(serialize(array(__('Cart rule removes point(s) from cart.'))));
                        return 0;
                        break;
                    case \J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_ADD:
                        $addedPoints += $rule->getPoints();
                        if ($rule->getPoints() > 0) {
                            $rules_message_cart[] = __('%1 point(s) added to total.', $rule->getPoints());
                        } else {
                            $rules_message_cart[] = __('%1 point(s) removed to total.', abs($rule->getPoints()));
                        }
                        break;
                    case \J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_MULTIPLY:
                        $defaultPoints = $defaultPoints * $rule->getPoints();
                        $rules_message_cart[] = __('Total multiplied by %1. New total is %2.', $rule->getPoints(), $defaultPoints);
                        break;
                    case \J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_DIVIDE:
                        $defaultPoints = $defaultPoints / $rule->getPoints();
                        $rules_message_cart[] = __('Total divided by %1. New total is %2.', $rule->getPoints(), $defaultPoints);
                        break;
                    case \J2t\Rewardpoints\Model\Cartpointrule::RULE_ACTION_TYPE_DONTPROCESS_USAGE:
                        return true;
                        break;
                }
            }
        }
        $cart->setRewardpointsCartRuleText(null);
        if (count($rules_message_cart)) {
            $cart->setRewardpointsCartRuleText(serialize($rules_message_cart));
        }
        return $defaultPoints + $addedPoints;
    }

    public function catalogRulesProcess($product, $defaultPoints, $qty = 1, $storeId = null, $groupId = null, $item = null) {
        $storeId = ($storeId) ? $storeId : $this->_storeManager->getStore()->getId();
        $groupId = ($groupId) ? $groupId : $this->session->getCustomerGroupId();
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
        $collection = $this->_catalogPointRulesCollection->create();
        $collection->setValidationFilter($websiteId, $groupId);
        $addedPoints = 0;
        //echo $collection->getSelect()->__toString();
        //die;

        if ($item != null) {
            $item->setRewardpointsCartRuleText(null);
        }

        $rules_message_cart = array();
        foreach ($collection as $rule) {
            if (!$rule->getStatus())
                continue;

            if ($rule->validate($product)) {
                switch ($rule->getActionType()) {
                    case \J2t\Rewardpoints\Model\Catalogpointrule::RULE_ACTION_TYPE_DONTPROCESS:
                        if ($item != null) {
                            $item->setRewardpointsCartRuleText(serialize(array(__('No points for this product.'))));
                        }
                        return 0;
                        break;
                    case \J2t\Rewardpoints\Model\Catalogpointrule::RULE_ACTION_TYPE_ADD:
                        if (abs($rule->getPoints())){
                            $addedPoints += $rule->getPoints() * $qty;
                            if ($item != null) {
                                if ($addedPoints > 0) {
                                    $rules_message_cart[] = __('%1 extra point(s) added.', $addedPoints);
                                } else if ($addedPoints < 0) {
                                    $rules_message_cart[] = __('%1 point(s) removed.', abs($addedPoints));
                                } else {
                                    $rules_message_cart[] = null;
                                }
                            }
                        } else {
                            $rules_message_cart[] = null;
                        }
                        break;
                    case \J2t\Rewardpoints\Model\Catalogpointrule::RULE_ACTION_TYPE_MULTIPLY:
                        $productPoints = $defaultPoints / $qty;
                        $productPointsMultiplied = $productPoints * $rule->getPoints();
                        $extraProductPoints = $productPointsMultiplied - $productPoints;
                        $addedPoints += $extraProductPoints * $qty;
                        if ($item != null && $addedPoints > 0) {
                            $rules_message_cart[] = __('Points multiplied by %1, %2 extra point(s) added.', $rule->getPoints(), $addedPoints);
                        } else {
                            $rules_message_cart[] = null;
                        }
                        break;
                    case \J2t\Rewardpoints\Model\Catalogpointrule::RULE_ACTION_TYPE_DIVIDE:
                        $productPoints = $defaultPoints / $qty;
                        $productPointsDivided = $productPoints / $rule->getPoints();
                        $removedProductPoints = $productPoints - $productPointsDivided;
                        $addedPoints -= $removedProductPoints * $qty;
                        if ($item != null && abs($addedPoints) > 0) {
                            $rules_message_cart[] = __('Points divided by %1, %2 point(s) removed.', $rule->getPoints(), abs($addedPoints));
                        } else {
                            $rules_message_cart[] = null;
                        }
                        break;
                }
            }
        }
        if (count($rules_message_cart) && $item != null) {
            $item->setRewardpointsCartRuleText(serialize($rules_message_cart));
        }
        return $defaultPoints + $addedPoints;
    }

    public function getModuleManager() {
        return $this->moduleManager;
    }

    public function canAddThis() {
        return $this->getAddThis() && $this->getAddThisAccount();
    }

    public function getReferralCustomCode($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_CUSTOM_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getReferralRedirection($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_REDIRECTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getReferrerCalculationType($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_POINT_METHOD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getFriendCalculationType($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_CHILD_POINT_METHOD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMinOrderAmount($storeId = null) {
        $min_value = $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_MIN_ORDER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        if ($step = $this->getStepValue()) {
            $min_value = min($this->getStepValue(), $step);
        }
        return $min_value;
    }

    public function getReferrerRegistrationPoints($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_REGISTRATION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getFriendRegistrationPoints($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_CHILD_REGISTRATION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getReferralPoint($storeId = null) {
        $storeId = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_POINTS, $storeId);
        //return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_POINTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getReferralChildPoint($storeId = null) {
        $storeId = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_CHILD_POINTS, $storeId);
        //return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_CHILD_POINTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAddThis($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_ADDTHIS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getAddThisAccount($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_ADDTHIS_ACCOUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getAddThisCode($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_ADDTHIS_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getReferralPerm($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REFERRAL_PERM, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getValidStatuses($storeId = null) {
        //$storeId = ($storeId) ? $storeId : \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue(self::XML_PATH_VALID_STATUSES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getValidUsedStatuses($storeId = null) {
        //$storeId = ($storeId) ? $storeId : \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $return = $this->_scopeConfig->getValue(self::XML_PATH_USED_STATUSES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        return ($return) ? $return . ',new' : 'new';
    }

    public function getStatusField($storeId = null) {
        //$storeId = ($storeId) ? $storeId : \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue(self::XML_PATH_STATUS_FIELD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isApplyStoreScope($storeId = null) {
        //$storeId = ($storeId) ? $storeId : \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue(self::XML_PATH_APPLY_STORE_SCOPE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPointsDelay($storeId = null) {
        //$storeId = ($storeId) ? $storeId : \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue(self::XML_PATH_VALIDITY_DELAY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPointsEventReview($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_EVENTS_REVIEW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    public function getPointsEventNewsletter($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_EVENTS_NESWLETTER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getPointsDuration($storeId = null) {
        //$storeId = ($storeId) ? $storeId : \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue(self::XML_PATH_VALIDITY_DURATION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function showOnProductView($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_SHOW_VIEW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function showOnProductList($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_SHOW_LIST, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function showDetails($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_POINT_DETAILS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function showAdminDashboard($storeId = null) {
        return (bool) $this->_scopeConfig->getValue(self::XML_PATH_POINT_DASHBOARD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getShowEquivalence($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_POINT_EQUIVELENCE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getShowBigImage($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_BIG_IMAGE_SHOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSizeBigImage() {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_BIG_IMAGE_SIZE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getURLBigImage($storeId = null) {
        $uploadDir = "rewardpoints/big";
        $fileName = $this->_scopeConfig->getValue(self::XML_PATH_BIG_IMAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        
        if (!$fileName){
            return $this->_assetRepo->getUrl('J2t_Rewardpoints::images/j2t_image_default.png');
        }
        
        $fileName = ($fileName) ? $fileName : 'default/j2t_image_big.png';
        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        
        if ($mediaDirectory->isFile($uploadDir . '/' . $fileName)) {
            return $this->_storeManager->getStore()->getBaseUrl(
                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . $uploadDir . '/' . $fileName;
        }
        return null;
        //return $this->_scopeConfig->getValue(self::XML_PATH_BIG_IMAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getShowSmallImage($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_SMALL_IMAGE_SHOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSizeSmallImage($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_SMALL_IMAGE_SIZE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getURLSmallImage($storeId = null) {
        $uploadDir = "rewardpoints/small";
        $fileName = $this->_scopeConfig->getValue(self::XML_PATH_SMALL_IMAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        
        if (!$fileName){
            return $this->_assetRepo->getUrl('J2t_Rewardpoints::images/j2t_image_default.png');
        }
        
        $fileName = ($fileName) ? $fileName : 'default/j2t_image_small.png';
        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        
        if ($mediaDirectory->isFile($uploadDir . '/' . $fileName)) {
            return $this->_storeManager->getStore()->getBaseUrl(
                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . $uploadDir . '/' . $fileName;
        }
        return null;
    }

    public function getActive($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function isSingleStoreModeEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SINGLE_STORE_MODE_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPointValue($quote = null, $storeId = null) {
        $defaultPointValue = $this->_scopeConfig->getValue(self::XML_PATH_MONEY_POINTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        if ($quote === null){
            return $defaultPointValue;
        }
        return $this->getCustomPointValue($quote, $defaultPointValue, true);
    }

    public function getRegistrationPoints($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_REGISTRATION_POINTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMaxOrderCollection($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_MAX_COLLECTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPointMax($points, $storeId = null) {
        if (!$this->getMaxOrderCollection($storeId)) {
            return $points;
        }
        return min($this->getMaxOrderCollection($storeId), $points);
    }

    public function getPointDiscountRequiredValue($quote = null, $storeId = null) {
        $defaultPointValue = (int) $this->_scopeConfig->getValue(self::XML_PATH_POINTS_MONEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        if ($quote === null){
            return $defaultPointValue;
        }
        return $this->getCustomPointValue($quote, $defaultPointValue, false);
    }

    public function getIncludeTax($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_PROCESS_TAX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getExcludeTax($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_EXCLUDE_TAX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getRemoveDiscountAmount($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_REMOVE_DISCOUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function isShippingDiscounted($storeId = null) {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_PROCESS_SHIPPING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMathMethodTotalEarn($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_MATH_TOTAL_EARN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    protected function mathActionOnTotalEarn($value, $storeId = null) {
        switch ($this->getMathMethodTotalEarn($storeId)) {
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_CEIL:
                $value = ceil($value);
                break;
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_FLOOR:
                $value = floor($value);
                break;
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_ROUND:
                $value = round($value);
                break;
        }
        return $value;
    }

    public function mathActionOnTotalCreditMemoPoints($value, $storeId = null) {
        return $this->mathActionOnTotalEarn($value, $storeId);
    }

    public function mathActionOnCatalogPages($value) {
        switch ($this->getMathMethodCatalogPages()) {
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_CEIL:
                $value = ceil($value);
                break;
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_FLOOR:
                $value = floor($value);
                break;
            case \J2t\Rewardpoints\Model\Config\Source\Mathmethod::MATH_ROUND:
                $value = round($value);
                break;
        }
        return $value;
    }

    public function getMathMethodCatalogPages($storeId = null) {
        return $this->_scopeConfig->getValue(self::XML_PATH_MATH_CATALOG_PAGES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    protected function getItemPoints($_item, $quote, $forcePointValue = false) {
        $item_qty = 1;
        if ($_item->getQty()) {
            $item_qty = $_item->getQty();
        } elseif ($_item->getQtyOrdered()) {
            $item_qty = $_item->getQtyOrdered();
        }
        //check TAX & CHECK DISCOUNT
        $item_price = $_item->getBaseRowTotal();
        if (!$this->getExcludeTax()) {
            //$item_price = $_item->getBaseRowTotalInclTax();
            $item_price += $_item->getBaseTaxAmount();
        }
        $discount = ($this->getRemoveDiscountAmount()) ? $_item->getBaseDiscountAmount() : 0;
        $item_price -= $discount;
        
        if ($forcePointValue !== false && is_numeric($forcePointValue)){
            $pointValue = $forcePointValue;
        } else {
            $pointValue = $this->getPointValue($quote, $quote->getStoreId());
        }
        $points = $item_price * $pointValue;
        //return $points;
        return $this->catalogRulesProcess($_item->getProduct(), $points, $item_qty, $quote->getStoreId(), $quote->getCustomerGroupId(), $_item);

    }

    public function getAllItemsPointsValue($items, $quote, $voidSetter = false, $forcePointValue = false) {
        $points = 0;
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                /* $this->_calculator->process($item);
                  $this->distributeDiscount($item);
                  foreach ($item->getChildren() as $child) {
                  $eventArgs['item'] = $child;
                  $this->_eventManager->dispatch('sales_quote_address_discount_item', $eventArgs);

                  $this->_aggregateItemDiscount($child);
                  } */
            } else {
                //$this->_calculator->process($item);
                //$this->_aggregateItemDiscount($item);
                $current_points = $this->getItemPoints($item, $quote, $forcePointValue);
                $points += $current_points;
                if (!$voidSetter){
                    $item->setRewardpointsGathered($this->getPointMax($current_points, $quote->getStoreId()));
                }
            }
        }
        return $this->mathActionOnTotalEarn($this->cartRulesProcess($quote, $points, false, $quote->getStoreId(), $quote->getCustomerGroupId()));
        //return $points;
    }

    public function getMinReferralOrderInCurrency($value) {
        return $this->coreData->currency($value, true, false);
    }

    public function getPointsProductPriceEquivalence($price, $storeId = null) {
        return $price * $this->getPointDiscountRequiredValue(null, $storeId);
    }

    public function getPointMoneyEquivalence($points, $noCurrency = false, $quote = null, $storeId = null) {
        if ($points && ($points_money = $this->getPointDiscountRequiredValue($quote, $storeId))) {
            if ($noCurrency) {
                return $points / $points_money;
            }
            return $this->coreData->currency($points / $points_money, true, false);
        }
        return null;
        //$formattedPrice = Mage::helper('core')->currency($this->convertPointsToMoneyEquivalence($points), true, false);
    }

    public function getProductPointsRange($product) {
        $range = [];
        $onlyUnicMandatory = false;

        if ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $points = $this->getProductPoints($product);
            if (!$points) {
                //list all products in grouped item
                $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
                $points = 0;
                foreach ($associatedProducts as $single_product) {
                    //TODO rules on product point
                    //$points += $this->getProductPoints($single_product, true, $from_list, false, null, null, $customer_group_id);
                    $points += $this->getProductPoints($single_product);
                }
            }
            return [$points];
        } else if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $options = $product->getTypeInstance()->getOptions($product);
            $minimalPrice = 0;
            $minimalPriceWithTax = 0;
            $hasRequiredOptions = false;
            if ($options) {
                foreach ($options as $option) {
                    if ($option->getRequired()) {
                        $hasRequiredOptions = true;
                    }
                }
            }
            $selectionMinimalPoints = [];
            $selectionMinimalPointsWithTax = [];

            if (!$options) {
                return $minimalPrice;
            }

            $isPriceFixedType = ($product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED);

            $min_acc = 0;
            $max_acc = 0;
            foreach ($options as $option) {
                $selections = $option->getSelections();
                if (count($selections)) {
                    $current_val = 0;
                    $current_vals = array();
                    foreach ($selections as $selection) {
                        /* @var $selection Mage_Bundle_Model_Selection */
                        if (!$selection->isSalable()) {
                            continue;
                        }
                        $subprice = $product->getPriceModel()->getSelectionPreFinalPrice($product, $selection, 1);
                        //$tierprice_incl_tax = Mage::helper('tax')->getPrice($product, $subprice, true);
                        //$tierprice_excl_tax = Mage::helper('tax')->getPrice($product, $subprice);

                        $tierprice_incl_tax = $this->_catalogData->getTaxPrice($product, $subprice, true);
                        $tierprice_excl_tax = $this->_catalogData->getTaxPrice($product, $subprice, false);

                        //$current_point = $this->getProductPoints($selection, $noCeil, $from_list, false, $tierprice_incl_tax, $tierprice_excl_tax, $customer_group_id);
                        $current_point = $this->getProductPoints($selection);
                        $current_vals[] = $current_point;
                    }

                    if ($option->getRequired() && !$onlyUnicMandatory || ($option->getRequired() && $onlyUnicMandatory && sizeof($selections) == 1)) {
                        $min_acc += min($current_vals);
                    }
                    $max_acc += max($current_vals);
                }
            }

            return array($min_acc, $max_acc);
        }
        return $range;
    }

    /**
     * 
     * @param Product $product
     * @return double
     */
    public function getProductPoints($product) {
        //TODO - check price with tax, etc...
        $product_points = $product->getRewardPoints();
        if ($product_points) {
            return $product_points;
        }
        $_finalPriceInclTax = $this->_taxData->getTaxPrice($product, $product->getFinalPrice(), true);
        //$_weeeTaxAmount = $this->_weeData->getAmount($product);
        $_weeeTaxAmount = $this->_weeData->getAmountExclTax($product);

        if ($this->getExcludeTax()) {
            $product_price = $this->_taxData->getTaxPrice($product, $product->getFinalPrice(), false);
            //$product_price = ($tierprice_excl_tax) ? $tierprice_excl_tax : $price;
        } else {
            $product_price = $_finalPriceInclTax + $_weeeTaxAmount;
            //$product_price = ($tierprice_incl_tax !== null) ? $tierprice_incl_tax : $price;
        }
        $points = $product_price * $this->getPointValue();
        return $this->catalogRulesProcess($product, $points, 1);
    }

    /* protected function getFullCustomerObject($customer)
      {
      // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
      // object passed for events
      $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
      $customerData = $this->dataProcessor
      ->buildOutputDataArray($customer, '\Magento\Customer\Api\Data\CustomerInterface');
      $mergedCustomerData->addData($customerData);
      $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
      return $mergedCustomerData;
      } */

    public function getCurrentCustomerPoints($customerId = null, $storeId = null, $flat = false) {
        if ($flat) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get('J2t\Rewardpoints\Model\Flatpoint')->loadByCustomerStore($customerId, $storeId)->getPointsCurrent();
        }
        return $this->getCustomerGatheredPoints($customerId, $storeId) - $this->getCustomerSpentPoints($customerId, $storeId) - $this->getCustomerExpiredPoints($customerId, $storeId);
    }

    public function getCustomerGatheredPoints($customerId = null, $storeId = null, $flat = false) {
        $customerId = ($customerId) ? $customerId : $this->session->getId();
        $storeId = ($storeId) ? $storeId : $this->_storeManager->getStore()->getId();
        if ($flat) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get('J2t\Rewardpoints\Model\Flatpoint')->loadByCustomerStore($customerId, $storeId)->getPointsCollected();
        }
        if ($customerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->get('J2t\Rewardpoints\Model\Point')->loadGatheredPoints($customerId, $storeId);
            return $model->getPoints();
        }
        return 0;
    }

    public function getCustomerSpentPoints($customerId = null, $storeId = null, $flat = false) {
        $customerId = ($customerId) ? $customerId : $this->session->getId();
        $storeId = ($storeId) ? $storeId : $this->_storeManager->getStore()->getId();
        if ($flat) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get('J2t\Rewardpoints\Model\Flatpoint')->loadByCustomerStore($customerId, $storeId)->getPointsUsed();
        }
        if ($customerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->get('J2t\Rewardpoints\Model\Point')->loadSpentPoints($customerId, $storeId);
            return $model->getPoints();
        }
        return 0;
    }

    public function getCustomerExpiredPoints($customerId = null, $storeId = null, $flat = false) {
        $customerId = ($customerId) ? $customerId : $this->session->getId();
        $storeId = ($storeId) ? $storeId : $this->_storeManager->getStore()->getId();
        if ($flat) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get('J2t\Rewardpoints\Model\Flatpoint')->loadByCustomerStore($customerId, $storeId)->getPointsLost();
        }
        if ($customerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get('J2t\Rewardpoints\Model\Point')->getPointsReceivedReajustment($customerId, $storeId);
        }
        return 0;
    }

    public function getCustomerNotAvailablePoints($customerId = null, $storeId = null, $flat = false) {
        $customerId = ($customerId) ? $customerId : $this->session->getId();
        $storeId = ($storeId) ? $storeId : $this->_storeManager->getStore()->getId();
        if ($flat) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get('J2t\Rewardpoints\Model\Flatpoint')->loadByCustomerStore($customerId, $storeId)->getPointsNotAvailable();
        }
        if ($customerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->get('J2t\Rewardpoints\Model\Point')->loadNotAvailableYetPoints($customerId, $storeId);
            return $model->getPoints();
        }
        return 0;
    }

    public function getCustomerWaitingValidationPoints($customerId = null, $storeId = null, $flat = false) {
        $customerId = ($customerId) ? $customerId : $this->session->getId();
        $storeId = ($storeId) ? $storeId : $this->_storeManager->getStore()->getId();
        if ($flat) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get('J2t\Rewardpoints\Model\Flatpoint')->loadByCustomerStore($customerId, $storeId)->getPointsWaiting();
        }
        if ($customerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->get('J2t\Rewardpoints\Model\Point')->loadPointsWaitingValidation($customerId, $storeId);
            return $model->getPoints();
        }
        return 0;
    }

    protected function getWebsiteStoreId($customer, $defaultStoreId = null) {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->_storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }
    
    
    public function sendAdminNotification($pointModel, $customer, $storeId, $points, $description) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $initialEnvironmentInfo = $this->_appEmulation->startEnvironmentEmulation($storeId);

        $this->inlineTranslation->suspend();
        $email = $objectManager->get('\Magento\Email\Model\Template');
        $template = $this->_scopeConfig->getValue(self::XML_PATH_NOTIFICATION_ADMIN_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $recipient = array(
            'email' => $customer->getEmail(),
            'name' => $customer->getName()
        );

        $transport = $this->_transportBuilder->setTemplateIdentifier(
                        $this->_scopeConfig->getValue(
                                self::XML_PATH_NOTIFICATION_ADMIN_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
                        )
                )->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $storeId,
                        ]
                )->setTemplateVars(
                        [
                            'points'   => $points,
                            'description'   => $description,
                            'customer' => $customer,
                            'rewardpoint'  => $pointModel
                        ]
                )->setFrom(
                        $this->_scopeConfig->getValue(
                                self::XML_PATH_NOTIFICATION_ADMIN_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
                        )
                )->addTo(
                        /* $this->_scopeConfig->getValue(
                          self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT,
                          \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                          ) */
                        $recipient
                )->getTransport();


        $return_val = $transport->sendMessage();
        $this->inlineTranslation->resume();
        return $return_val;
        
        
    }

    public function sendNotification($customer, $store, $points, $days) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $initialEnvironmentInfo = $this->_appEmulation->startEnvironmentEmulation($store->getId());

        $this->inlineTranslation->suspend();
        $email = $objectManager->get('\Magento\Email\Model\Template');
        $template = $this->_scopeConfig->getValue(self::XML_PATH_NOTIFICATION_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
        $recipient = array(
            'email' => $customer->getEmail(),
            'name' => $customer->getName()
        );

        $transport = $this->_transportBuilder->setTemplateIdentifier(
                        $template
                )->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $store->getId(),
                        ]
                )->setTemplateVars(
                        [
                            'points' => $points,
                            'days' => $days,
                            'customer' => $customer
                        ]
                )->setFrom(
                        $this->_scopeConfig->getValue(
                                self::XML_PATH_NOTIFICATION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()
                        )
                )->addTo(
                        /* $this->_scopeConfig->getValue(
                          self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT,
                          \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                          ) */
                        $recipient
                )->getTransport();


        $return_val = $transport->sendMessage();
        $this->inlineTranslation->resume();
        return $return_val;
    }

    public function sendCustomerNotification($customer, $store, $points, $pointModel, $sender = null, $emailTemplate = null) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $initialEnvironmentInfo = $this->_appEmulation->startEnvironmentEmulation($store->getId());

        $this->inlineTranslation->suspend();

        $email = $objectManager->get('\Magento\Email\Model\Template');
        $template = $this->_scopeConfig->getValue(self::XML_PATH_CUSTOMER_NOTIFICATION_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());

        if ($emailTemplate != null) {
            $template = $emailTemplate;
        }

        $recipient = array(
            'email' => $customer->getEmail(),
            'name' => $customer->getName()
        );
        
        if (!$sender){
            $sender = $this->_scopeConfig->getValue(
                                self::XML_PATH_NOTIFICATION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()
                        );
        }

        $transport = $this->_transportBuilder->setTemplateIdentifier(
                        $this->_scopeConfig->getValue(
                                self::XML_PATH_CUSTOMER_NOTIFICATION_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()
                        )
                )->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $store->getId(),
                        ]
                )->setTemplateVars(
                        [
                            'points' => $points,
                            'customer' => $customer,
                            'point_model' => $pointModel
                        ]
                )->setFrom(
                        $sender
                )->addTo(
                        /* $this->_scopeConfig->getValue(
                          self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT,
                          \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                          ) */
                        $recipient
                )->getTransport();

        $return_val = $transport->sendMessage();
        $this->inlineTranslation->resume();
        return $return_val;
    }

    /**
     * 
     * @param type $order
     * @param type $quote
     * @return type
     * processReferralTreatment / original method: sales_order_success_referral
     */
    public function processReferralTreatment($order, $quote = null) {
        if (!$order->getCustomerId()) {
            return;
        }
        if (!is_object($quote) || ($quote == null && !$quote->getId())) {
            $quote = $this->_quoteFactory->create()->load($order->getQuoteId());
        }
        
        $userId = 0;
        $customer = $this->customerFactory->create()->load($order->getCustomerId());
        
        if ($this->rewardSession->getReferralUser() == $order->getCustomerId() || $quote->getRewardpointsReferrer() == $order->getCustomerId()) {
            $this->rewardSession->setReferralUser(null);
            $quote->setRewardpointsReferrer(null);
        }
        if ((int)$customer->getRewardpointsReferrer()) {
            $userId = (int)$customer->getRewardpointsReferrer();
        } else if ($this->rewardSession->getReferralUser() && $this->rewardSession->getReferralUser() != $order->getCustomerId()) {
            $userId = (int)$this->rewardSession->getReferralUser();
        } else if ((int) $quote->getRewardpointsReferrer() && $quote->getRewardpointsReferrer() != $order->getCustomerId()) {
            $userId = (int) $quote->getRewardpointsReferrer();
        }
        
        //check if referral from link...
        if ($userId) {
            $referrer = $this->customerFactory->create()->load($userId);
            $referree_email = $order->getCustomerEmail();
            $referree_name = $order->getCustomerName();

            $referralModel = $this->referralFactory->create();
            $verifyStoreId = null;
            if ($this->isApplyStoreScope($order->getStoreId())){
                $verifyStoreId = $order->getStoreId();
            }
            if (!$referralModel->isSubscribed($referree_email, $verifyStoreId) && $referrer->getEmail() != $referree_email) {
                
                $referralModel = $referralModel->loadByEmail($referree_email, $verifyStoreId);
                $referralModel->setRewardpointsReferralParentId($userId)
                        ->setRewardpointsReferralEmail($referree_email)
                        ->setRewardpointsReferralName($referree_name)
                        ->setStoreId($order->getStoreId());
                
                $referralModel->save();
            }
            $this->rewardSession->setReferralUser(null);
            $this->rewardSession->unsetAll();
        }
        
        $rewardPoints = $this->getReferralPoint($order->getStoreId());
        $rewardPointsChild = $this->getReferralChildPoint($order->getStoreId());
        $rewardPointsReferralMinOrder = $this->getMinOrderAmount($order->getStoreId());
        
        
        $referralPointMethod = $this->getReferrerCalculationType($order->getStoreId());
        $rewardPoints = $this->referralPointsEntry($order, $referralPointMethod, $rewardPoints);
        $rewardPointsChild = $this->referralChildPointsEntry($order, $rewardPointsChild);

        if (!$order->getBaseSubtotalInclTax()) {
            $order->setBaseSubtotalInclTax($order->getBaseSubtotal() + $order->getBaseTaxAmount());
        }

        $base_subtotal = $order->getBaseSubtotalInclTax();
        if ($this->getExcludeTax($order->getStoreId())) {
            $base_subtotal = $base_subtotal - $order->getBaseTaxAmount();
        }

        if (($rewardPoints > 0 || $rewardPointsChild > 0 && $order->getCustomerEmail()) && ($rewardPointsReferralMinOrder == 0 || $rewardPointsReferralMinOrder <= $base_subtotal)) {
            $this->processReferralInsertion($order, $rewardPoints, $rewardPointsChild, false, $order->getStoreId());
        }

        /*
         * TODO: process advanced referral points / only valid orders
         */
    }
    
    protected function referralChildPointsEntry($order, $rewardPointsChild) {
        $referralChildPointMethod = $this->getFriendCalculationType($order->getStoreId());
        if ($referralChildPointMethod == \J2t\Rewardpoints\Model\Config\Source\Calculationtype::RATIO_POINTS) {
            $rate = $order->getBaseToOrderRate();
            if ($rewardPointsChild > 0) {
                $items = $order->getAllVisibleItems();
                $points = $this->getAllItemsPointsValue($items, $order->getQuote(), true, $rate);
                $rewardPointsChild = $this->getPointMax($points, $order->getStoreId());
            }
        } else if ($referralChildPointMethod == \J2t\Rewardpoints\Model\Config\Source\Calculationtype::CART_SUMMARY) {
            if (!$order->getBaseSubtotalInclTax()) {
                $order->setBaseSubtotalInclTax($order->getBaseSubtotal() + $order->getBaseTaxAmount());
            }
            if (($base_subtotal = $order->getBaseSubtotalInclTax()) && $rewardPointsChild > 0) {
                $summary_points = $base_subtotal * $rewardPointsChild;
                if ($this->getExcludeTax($order->getStoreId())) {
                    $summary_points = $summary_points - $order->getBaseTaxAmount();
                }
                $rewardPointsChild = $this->processMathValue($summary_points);
            }
        }
        return $rewardPointsChild;
    }
    
    public function referralPointsEntry($order, $referralPointMethod, $rewardPoints = 0) {
        if (!$order->getBaseSubtotalInclTax()) {
            $order->setBaseSubtotalInclTax($order->getBaseSubtotal() + $order->getBaseTaxAmount());
        }
        if ($referralPointMethod == \J2t\Rewardpoints\Model\Config\Source\Calculationtype::RATIO_POINTS) {
            $rate = $order->getBaseToOrderRate();
            if ($rewardPoints > 0) {
                $items = $order->getAllVisibleItems();
                $points = $this->getAllItemsPointsValue($items, $order->getQuote(), true, $rate);
                $rewardPointsChild = $this->getPointMax($points);
            }
        } else if ($referralPointMethod == \J2t\Rewardpoints\Model\Config\Source\Calculationtype::CART_SUMMARY) {
            if (($base_subtotal = $order->getBaseSubtotalInclTax()) && $rewardPoints > 0) {
                $summary_points = $base_subtotal * $rewardPoints;
                if ($this->getExcludeTax($order->getStoreId())) {
                    $summary_points = $summary_points - $order->getBaseTaxAmount();
                }
                $rewardPoints = $this->processMathValue($summary_points);
            }
        }
        return $rewardPoints;
    }
    
    public function processReferralInsertion($order, $rewardPoints, $rewardPointsChild, $escape_status_verification = false, $storeId = null) {
        $referralModel = $this->referralFactory->create();
        
        $verifyStoreId = null;
        if ($this->isApplyStoreScope($order->getStoreId())){
            $verifyStoreId = $order->getStoreId();
        }
        if ($referralModel->isSubscribed($order->getCustomerEmail(), $verifyStoreId)) {
            if (!$referralModel->isConfirmed($order->getCustomerEmail(), $escape_status_verification, $verifyStoreId)) {
                
                $referralModel->loadByEmail($order->getCustomerEmail(), $verifyStoreId);
                $referralModel->setData('rewardpoints_referral_status', true);
                $referralModel->setData('rewardpoints_referral_child_id', $order->getCustomerId());
                $referralModel->setData('store_id', $order->getStoreId());
                $referralModel->save();

                $parent = $this->customerFactory->create()->load($referralModel->getData('rewardpoints_referral_parent_id'));
                $child = $this->customerFactory->create()->load($referralModel->getData('rewardpoints_referral_child_id'));
                
                //$helper->recordPoints(\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REGISTRATION, $customerId, $customer->getStoreId(), date('Y-m-d'), $points, 0, true, $helper->getPointsDelay(), $helper->getPointsDuration(), null, null);
                
                try {
                    if ($rewardPoints > 0) {
                        $reward_model = $this->_pointFactory->create();
                        $post = ['order_id' => $order->getIncrementId(), 'customer_id' => $referralModel->getData('rewardpoints_referral_parent_id'),
                            'store_id' => $order->getStoreId(), 'points_current' => $rewardPoints, 'rewardpoints_referral_id' => $referralModel->getData('rewardpoints_referral_id')];
                        
                        foreach ($post as $key => $value){
                            $reward_model->setData($key, $value);
                        }
                        //$reward_model->setData($post);
                        $reward_model->save();
                    }

                    if ($rewardPointsChild > 0) {
                        $reward_model = $this->_pointFactory->create();
                        $post = ['order_id' => $order->getIncrementId(), 'customer_id' => $referralModel->getData('rewardpoints_referral_child_id'),
                            'store_id' => $order->getStoreId(), 'points_current' => $rewardPointsChild, 'rewardpoints_referral_id' => $referralModel->getData('rewardpoints_referral_id')];
                        foreach ($post as $key => $value){
                            $reward_model->setData($key, $value);
                        }
                        //$reward_model->setData($post);
                        $reward_model->save();
                    }
                } catch (Exception $e) {
                    //Mage::getSingleton('session')->addError($e->getMessage());
                }
                $referralModel->sendConfirmation($parent, $child, $parent->getEmail(), $parent->getName(), $storeId);
            }
        }
    }

    public function sendConfirmation($referral_object, $parent_customer, $child_customer, $destination, $destination_name, $storeId = null, $sendermailStoreId = null) {
        
        
        $this->inlineTranslation->suspend();
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($parent_customer, $sendemailStoreId);
        }
        
        $store = $this->_storeManager->getStore($parent_customer->getStoreId());
        $sender = $this->_scopeConfig->getValue(self::XML_PATH_CONFIRMATION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

        $this->sendEmailTemplate(
            $destination, $destination_name, self::XML_PATH_CONFIRMATION_EMAIL_TEMPLATE, $sender, ['parent' => $parent_customer, 'child' => $child_customer, 'referral' => $referral_object, 'store' => $store], $storeId
        );
        $this->inlineTranslation->resume();

        return $this;
    }

    public function sendSubscription($referral_object, $parent_customer, $destination, $destination_name, $storeId = null, $sendemailStoreId = null) {
        
        $this->inlineTranslation->suspend();
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($parent_customer, $sendemailStoreId);
        }
        
        $store = $this->_storeManager->getStore($parent_customer->getStoreId());
        //$customerEmailData = $this->getFullCustomerObject($parent_customer);
        
        if ($this->_scopeConfig->getValue(self::XML_PATH_SUBSCRIPTION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId) == 'user-email-address') {
            $sender = array(
                'name' => strip_tags($parent_customer->getFirstname() . ' ' . $parent_customer->getLastname()),
                'email' => strip_tags($parent_customer->getEmail())
            );
        } else if ($this->_scopeConfig->getValue(self::XML_PATH_SUBSCRIPTION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId) != 'user-email-address') {
            $sender = $this->_scopeConfig->getValue(self::XML_PATH_SUBSCRIPTION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } 
        
        $real_url = $store->getUrl('rewardpoints/index/goReferral', ["referrer" => $parent_customer->getId()]);
        $used_url = $store->getUrl('', ["referral-program" => str_replace('/', '-', base64_encode($parent_customer->getId() . 'j2t'))]);
        
        $return_value = $this->sendEmailTemplate(
            $destination, $destination_name, self::XML_PATH_SUBSCRIPTION_EMAIL_TEMPLATE, $sender, ['parent' => $parent_customer, 'referral' => $referral_object, 'referral_url' => $used_url, 'store' => $store], $storeId
        );

        $this->inlineTranslation->resume();

        return $return_value;

        //return $this;
    }
    
    
    protected function sendEmailTemplate($email, $name, $template, $sender, $templateParams = [], $storeId = null) {
        /** @var \Magento\Framework\Mail\TransportInterface $transport */
        $this->inlineTranslation->suspend();
        $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_scopeConfig->getValue($template, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
                )->setTemplateOptions(
                        ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                        $templateParams
                )->setFrom(
                        //$this->_scopeConfig->getValue($sender, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
                        $sender
                )->addTo(
                        $email, $name
                        /* $customer->getEmail(),
                          $this->customerViewHelper->getCustomerName($customer) */
                )->getTransport();
        $return = true;
        try {
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $e) {
            /** @var \Magento\Newsletter\Model\Problem $problem */
            $return = false;
        }
        $this->inlineTranslation->resume();
        return $return;
        //$transport->sendMessage();
        //return $this;
    }
    
    public function recordPoints($orderId, $customerId, $storeId, $dateInsertion = null, $points_gathered = 0, $points_used = 0, $reload_object = false, $delay = null, $end_days = null, $state = null, $status = null, $bypassObjectReload = false, $processOnce = false) {
        
        $date_start = $date_end = null;

        $add_delay = 0;
        if ($delay && is_numeric($delay)) {
            $date_start = $this->_dateTime->formatDate(mktime(0, 0, 0, date("m"), date("d") + $delay, date("Y")));
            $add_delay = $delay;
        }
        if ($end_days && is_numeric($end_days)) {
            if ($date_start) {
                $date_end = $this->_dateTime->formatDate(time());
            }
            $date_end = $this->_dateTime->formatDate(mktime(0, 0, 0, date("m"), date("d") + $end_days + $add_delay, date("Y")));
        }

        $dateInsertion = ($dateInsertion) ? $dateInsertion : date('Y-m-d');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->get('J2t\Rewardpoints\Model\Point');
        //if ($verify_insertion){
        
        if (!$bypassObjectReload){
            $model = $model->loadByIncrementId($orderId, $customerId);
            if ($processOnce && $model->getId()){
                return $this;
            }
        }
        
        //}

        $model->setCustomerId($customerId)
                ->setStoreId($storeId)
                ->setOrderId($orderId)
                ->setPointsCurrent($points_gathered)
                ->setPointsSpent($points_used)
                ->setDateOrder($dateInsertion)
                ->setDateInsertion($dateInsertion)
                ->setDateStart($date_start)
                ->setDateEnd($date_end)
                ->setRewardpointsState($state)
                ->setRewardpointsStatus($status)
                ->save();
    }

    public function getAllowAttributes($product) {
        return $product->getTypeInstance()->getConfigurableAttributes($product);
    }

    public function getConfigurableOptions($currentProduct, $allowedProducts) {
        $options = [];

        $basePoints = $this->getProductPoints($currentProduct);
        $baseEquivalence = $this->getPointMoneyEquivalence($basePoints);

        foreach ($allowedProducts as $product) {
            $productId = $product->getId();

            $points = $this->getProductPoints($product);

            foreach ($this->getAllowAttributes($currentProduct) as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                $options['points'][$productAttributeId][$attributeValue][$productId] = $points;
                $options['equivalence'][$productAttributeId][$attributeValue][$productId] = $this->getPointMoneyEquivalence($points);
            }
        }


        $options['basePoints'] = $basePoints;
        $optinos['baseEquivalence'] = $this->getPointMoneyEquivalence($baseEquivalence);

        return $options;
    }

}
