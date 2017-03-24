<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer Point list block
 */
class Points extends \Magento\Customer\Block\Account\Dashboard {

    protected $_template = 'points.phtml';

    /**
     * Customer Points collection
     *
     * @var \J2t\Rewardpoints\Model\Resource\Point\Collection
     */
    protected $_collection;

    /**
     * Review resource model
     *
     * @var \J2t\Rewardpoints\Model\Resource\Point\Collection\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer, $_customerSession, $_pointData, $_orderFactory;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory, CustomerRepositoryInterface $customerRepository, AccountManagementInterface $customerAccountManagement, \J2t\Rewardpoints\Model\Resource\Point\CollectionFactory $collectionFactory, \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Sales\Model\OrderFactory $orderFactory, array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_pointData = $pointHelper;
        $this->_orderFactory = $orderFactory;
        parent::__construct(
                $context, $customerSession, $subscriberFactory, $customerRepository, $customerAccountManagement, $data
        );
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Initialize review collection
     *
     * @return $this
     */
    protected function _initCollection() {
        $this->_collection = $this->_collectionFactory->create();
        if ($this->_pointData->isApplyStoreScope()){
            $this->_collection
                ->setStoreFilter($this->_storeManager->getStore()->getId());
        }
        $this->_collection
                ->setUserFilter($this->currentCustomer->getCustomerId())
                ->setDateOrder();
        
        
        
        return $this;
    }

    /**
     * Gets collection items count
     *
     * @return int
     */
    public function count() {
        return $this->_getCollection()->getSize();
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml() {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout() {
        $toolbar = $this->getLayout()->createBlock(
                    'Magento\Theme\Block\Html\Pager', 'customer_points_list.toolbar'
            )->setCollection(
            $this->getCollection()
        );

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * Get collection
     *
     * @return \Magento\Review\Model\Resource\Review\Product\Collection
     */
    protected function _getCollection() {
        if (!$this->_collection) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    /**
     * Get collection
     *
     * @return \Magento\Review\Model\Resource\Review\Product\Collection
     */
    public function getCollection() {
        return $this->_getCollection();
    }

    /* public function getReviewLink()
      {
      return $this->getUrl('review/customer/view/');
      } */


    /* public function getProductLink()
      {
      return $this->getUrl('catalog/product/view/');
      } */

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date) {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }

    /**
     * Add review summary
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml() {
        //$this->_getCollection()->load()->addReviewSummary();
        $this->_getCollection()->load();
        return parent::_beforeToHtml();
    }

    public function getTypeOfPoint($_point, $referral_id = null) {
        $order_id = $_point->getOrderId();
        $referral_id = $_point->getRewardpointsReferralId();
        $quote_id = $_point->getQuoteId();
        $description = ($_point->getRewardpointsDescription()) ? ' - ' . $_point->getRewardpointsDescription() : '';
        $description_dyn = ($_point->getRewardpointsDescription()) ? __($_point->getRewardpointsDescription()) : __('Event Points');

        $status_field = $this->_pointData->getStatusField();

        $toHtml = '';
        if ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_REFERRAL_REGISTRATION) {
            //rewardpoints_linker
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->create('J2t\Rewardpoints\Model\Point')->load($_point->getRewardpointsLinker());
            if ($model->getName()) {
                $toHtml .= '<div class="j2t-in-title">' . __('Referral registration points (%1)', $model->getName()) . '</div>';
            } else {
                $toHtml .= '<div class="j2t-in-title">' . __('Referral registration points') . '</div>';
            }
        } else if ($referral_id) {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $customer = $objectManager->create('Magento\Customer\Model\CustomerFactory')->create();
            $referrer = $objectManager->create('J2t\Rewardpoints\Model\Referral')->load($referral_id);
            if ($referrer->getRewardpointsReferralParentId() && $this->currentCustomer->getCustomer() && is_object($this->currentCustomer->getCustomer()) && $referrer->getRewardpointsReferralParentId() != $this->currentCustomer->getCustomer()->getId() && ($customer_model = $customer->load($referrer->getRewardpointsReferralParentId()))) {
                $toHtml .= '<div class="j2t-in-title">' . __('Referral points (%1)', $customer_model->getName()) . '</div>';
            } else if ($referrer->getRewardpointsReferralParentId() && $this->currentCustomer->getCustomer() && is_object($this->currentCustomer->getCustomer()) && $referrer->getRewardpointsReferralChildId() != $this->currentCustomer->getCustomer()->getId() && ($customer_model = $customer->load($referrer->getRewardpointsReferralChildId()))) {

                $toHtml .= '<div class="j2t-in-title">' . __('Referral points (%1)', $customer_model->getName()) . '</div>';
            } else {
                $toHtml .= '<div class="j2t-in-title">' . __('Referral points (%1)', $referrer->getRewardpointsReferralEmail()) . '</div>';
            }


            $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($order_id);
            $toHtml .= '<div class="j2t-in-txt">' . __('Referral order (#%1) state: %2', $order_id, $order->getConfig()->getStatusLabel($order->getData($status_field))) . '</div>';



            /* $referrer = Mage::getModel('rewardpoints/referral')->load($referral_id);
              $model = Mage::getModel('customer/customer')->load($_point->getRewardpointsLinker());
              if ($referrer->getRewardpointsReferralParentId() && Mage::getSingleton('customer/session')->getCustomer()
              && is_object(Mage::getSingleton('customer/session')->getCustomer())
              && $referrer->getRewardpointsReferralParentId() != Mage::getSingleton('customer/session')->getCustomer()->getId()
              && ($customer_model = Mage::getModel('customer/customer')->load($referrer->getRewardpointsReferralParentId()))){
              $toHtml .= '<div class="j2t-in-title">'.$this->__('Referral points (%s)',$customer_model->getName()).'</div>';
              } else if ($referrer->getRewardpointsReferralParentId() && Mage::getSingleton('customer/session')->getCustomer()
              && is_object(Mage::getSingleton('customer/session')->getCustomer())
              && $model->getRewardpointsReferralChildId() != Mage::getSingleton('customer/session')->getCustomer()->getId()
              && ($customer_model = Mage::getModel('customer/customer')->load($referrer->getRewardpointsReferralChildId()))){
              $toHtml .= '<div class="j2t-in-title">'.$this->__('Referral points (%s)',$customer_model->getName()).'</div>';
              } else {
              $toHtml .= '<div class="j2t-in-title">'.$this->__('Referral points (%s)',$referrer->getRewardpointsReferralEmail()).'</div>';
              }

              $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
              $toHtml .=  '<div class="j2t-in-txt">'.$this->__('Referral order (#%s) state: %s', $order_id, $this->__($order->getData($status_field))).'</div>'; */
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_REVIEW) {
            $toHtml .= '<div class="j2t-in-title">' . __('Review points') . '</div>';
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_DYN) {
            $toHtml .= '<div class="j2t-in-title">' . $description_dyn . '</div>';
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_NEWSLETTER) {
            $toHtml .= '<div class="j2t-in-title">' . __('Newsletter points') . '</div>';
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_POLL) {
            $toHtml .= '<div class="j2t-in-title">' . __('Poll participation points') . '</div>';
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_TAG) {
            $toHtml .= '<div class="j2t-in-title">' . __('Tag points') . '</div>';
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_GP) {
            //TODO - order link
            /* if ($_point->getRewardpointsLinker()){
              $extra_relation = "";
              $product = Mage::getModel('catalog/product')->load($_point->getRewardpointsLinker());
              if ($product_name = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name')){
              $extra_relation = "<div>".$this->__('Related to product: %s', $product_name)."</div>";
              }
              }
              $toHtml .= '<div class="j2t-in-title">'.$this->__('Google Plus points').'</div>'.$extra_relation; */
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_FB) {
            //TODO
            /* if ($_point->getRewardpointsLinker()){
              $extra_relation = "";
              $product = Mage::getModel('catalog/product')->load($_point->getRewardpointsLinker());
              if ($product_name = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name')){
              $extra_relation = "<div>".$this->__('Related to product: %s', $product_name)."</div>";
              }
              }
              $toHtml .= '<div class="j2t-in-title">'.$this->__('Facebook Like points').'</div>'.$extra_relation; */
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_PIN) {
            //TODO
            /* if ($_point->getRewardpointsLinker()){
              $extra_relation = "";
              $product = Mage::getModel('catalog/product')->load($_point->getRewardpointsLinker());
              if ($product_name = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name')){
              $extra_relation = "<div>".$this->__('Related to product: %s', $product_name)."</div>";
              }
              }
              $toHtml .= '<div class="j2t-in-title">'.$this->__('Pinterest points').'</div>'.$extra_relation; */
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_TT) {
            //TODO
            /* if ($_point->getRewardpointsLinker()){
              $extra_relation = "";
              $product = Mage::getModel('catalog/product')->load($_point->getRewardpointsLinker());

              if ($product_name = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name')){
              $extra_relation = "<div>".$this->__('Related to product: %s', $product_name)."</div>";
              }
              }
              $toHtml .= '<div class="j2t-in-title">'.$this->__('Twitter points').'</div>'.$extra_relation; */
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_REQUIRED) {
            //TODO
            /* $current_order = Mage::getModel('sales/order')->loadByAttribute('quote_id', $quote_id);
              $points_txt = $this->__('Points used on products for order %s', $current_order->getIncrementId());
              $toHtml .= '<div class="j2t-in-title">'.$points_txt.'</div>'; */
        } elseif ($order_id == \J2t\Rewardpoints\Model\Point::TYPE_POINTS_BIRTHDAY) {
            if (isset($points_name[$order_id])) {
                $toHtml .= '<div class="j2t-in-title">' . $points_name[$order_id] . '</div>';
            } else {
                $toHtml .= '<div class="j2t-in-title">' . __('Birthday points') . '</div>';
            }
        } elseif ($order_id < 0) {
            $points_name = [
                \J2t\Rewardpoints\Model\Point::TYPE_POINTS_REVIEW => __('Review points'),
                \J2t\Rewardpoints\Model\Point::TYPE_POINTS_ADMIN => __('Store input %1', $description),
                \J2t\Rewardpoints\Model\Point::TYPE_POINTS_REGISTRATION => __('Registration points')
            ];
            if (isset($points_name[$order_id])) {
                $toHtml .= '<div class="j2t-in-title">' . $points_name[$order_id] . '</div>';
            } else {
                $toHtml .= '<div class="j2t-in-title">' . __('Gift') . '</div>';
            }
        } else {
            $toHtml .= '<div class="j2t-in-title">' . __('Order: %1', $order_id) . '</div>';
            //$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
            $order = $this->_orderFactory->create();
            $order->loadByIncrementId($order_id);

            $status_label = $order->getConfig()->getStatusLabel($order->getData($status_field));
            $status_label = ($status_label != "") ? $status_label : $order->getData($status_field);
            if ($status_label == 'Canceled') {
                $toHtml .= '<div class="j2t-in-txt">' . __('Order state: %1', $status_label) . '</div>';
            }

            //$toHtml .= '<div class="j2t-in-txt">'.__('Order state: %1',__($order->getData($status_field))).'</div>';
        }

        if ($this->_pointData->getModuleManager()->isEnabled('J2t_Rewardshare')) {
            if ($order_id == \J2t\Rewardshare\Model\Point::TYPE_POINTS_SHARE) {
                $toHtml = '<div class="j2t-in-title">' . __('Gift (shared points)') . '</div>';
            }
        }

        return $toHtml;
    }

}
