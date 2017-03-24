<?php

namespace J2t\Rewardpoints\Model;

//use Magento\App\ObjectManager;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Observer {

    protected $_response, $_url, $_request, $_actionFlag;
    protected $_dateFilter;
    protected $_storeManager;
    protected $_pointData = null;

    public function __construct(
    \Magento\Framework\App\Response\Http $response, \Magento\Framework\UrlInterface $url, \Magento\Framework\App\ActionFlag $actionFlag, \Magento\Framework\App\RequestInterface $request, \Magento\Store\Model\StoreManagerInterface $storeManager, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
    ) {
        $this->_storeManager = $storeManager;
        $this->_response = $response;
        $this->_url = $url;
        $this->_request = $request;
        $this->_actionFlag = $actionFlag;
        $this->_pointData = $pointHelper;
        $this->_dateFilter = $dateFilter;
    }

    public function preDispatch(\Magento\Framework\Event\Observer $observer) {
        //$this->_response->appendBody('Hello World');
    }

    public function modifyNoRoutePage(\Magento\Framework\Event\Observer $observer) {
        //var_dump($this->_response);
        if (strpos($this->_request->getPathInfo(), "referral-program") !== false) {
            $actionName = str_replace("referral-program", "", $this->_request->getPathInfo());

            $path_info = pathinfo($this->_request->getPathInfo());

            if (isset($path_info['filename'])) {
                $path = $path_info['filename'];
            } else {
                $path = substr($this->_request->getPathInfo(), 1, -1);
                $path = str_replace("referral-program/", "", $path);
            }

            $requestUri = $this->_url->getUrl('rewardpoints/referral/goReferral', ['_current' => true, 'decrypt' => $path]);
            $this->_response->setRedirect($requestUri);
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            return true;
        }
    }

    /**
     * 
     * Substract credit memo points
     * @param \Magento\Framework\Event\Observer $observer
     * 
     */
    public function addRewardRefund(\Magento\Framework\Event\Observer $observer) {
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $order = $creditMemo->getOrder();

        /* $creditMemoPost = $this->_request->getPost('creditmemo');
          if(isset($creditMemoPost['gathered_points']) && $creditMemoPost['gathered_points'] > 0){
          $creditMemo->setData('gathered_points', $creditMemoPost['gathered_points']);
          }
          if(isset($creditMemoPost['used_points']) && $creditMemoPost['used_points'] > 0){
          $creditMemo->setData('used_points', $creditMemoPost['used_points']);
          } */
        $pointsRefunded = $creditMemo->getRewardpointsGathered();
        $pointsRefundedUsed = $creditMemo->getRewardpointsUsed();

        if ($order->getIncrementId() && $order->getCustomerId() && ($pointsRefunded > 0 || $pointsRefundedUsed > 0)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->create('J2t\Rewardpoints\Model\Point')->loadByIncrementId($order->getIncrementId(), $order->getCustomerId());
            $currentPoints = $model->getPointsCurrent();
            $usedPoints = $model->getPointsSpent();
            $model->setPointsCurrent($currentPoints - $pointsRefunded);
            $model->setPointsSpent($usedPoints - $pointsRefundedUsed);
            $model->save();
        }
    }

    public function addRewardPointsAdmin(\Magento\Framework\Event\Observer $observer) {
        
        $request = $observer->getRequestModel();
        $orderModel = $observer->getOrderCreateModel();

        $data = $request->getPost('order');
        $quote = $orderModel->getQuote();

        if (isset($data['rewardpoints']['qty']) && is_object($orderModel) && is_object($quote) && $quote->getId()) {
            if (is_numeric($data['rewardpoints']['qty'])) {
                $points = $data['rewardpoints']['qty'];
                $customerPoints = 0;
                if (($customerId = $quote->getCustomerId()) && ($storeId = $quote->getStoreId())) {
                    $customerPoints = $this->_pointData->getCurrentCustomerPoints($customerId, $storeId);
                }
                $points = ($customerPoints < $points) ? $customerPoints : $points;

                //$quote->setRewardpointsQuantity($points)->collectTotals();
                $quote->setRewardpointsQuantity($points)->setRecollect(true);
                /* if ($points > 0){
                  Mage::helper('rewardpoints/event')->setCreditPoints($points);
                  $quote->setRewardpointsQuantity($points);
                  //->save();
                  } else {
                  Mage::getSingleton('rewardpoints/session')->setProductChecked(0);
                  Mage::helper('rewardpoints/event')->setCreditPoints(0);
                  $quote
                  ->setRewardpointsQuantity(NULL)
                  ->setRewardpointsDescription(NULL)
                  ->setBaseRewardpoints(NULL)
                  ->setRewardpoints(NULL);
                  }
                  $orderModel->setRecollect(true); */
            }
        }
    }

    public function addRewardFormAdmin(\Magento\Framework\Event\Observer $observer) {
        $block = $observer->getBlock();
        $request = $block->getRequest();

        if (($block->getNameInLayout() == 'coupons' || $block->getBlockAlias() == 'coupons') && ($request->getControllerName() == "order_create" || $request->getControllerName() == "order_edit")) {

            $extraBlock = $block->getLayout()->createBlock('J2t\Rewardpoints\Block\Adminhtml\Createorders\Reward');
            $extraBlock->setTemplate('form.phtml');
            $extraBlock->setNameInLayout("reward_coupons");
            $extraHtml = $extraBlock->toHtml();
            echo $extraHtml;
            //TODO: check if module is active in order to show new block
            /* $transport          = $observer->getTransport();
              $fileName           = $block->getTemplateFile();
              $thisClass          = get_class($block);

              $html = $transport->getHtml();
              $magento_block = Mage::getSingleton('core/layout');
              $productsHtml = $magento_block->createBlock('rewardpoints/adminhtml_createorder_reward');
              $productsHtml->setTemplate('rewardpoints/form.phtml');
              $productsHtml->setNameInLayout("reward_coupons");
              $extraHtml    = $productsHtml->toHtml();
              $transport->setHtml($extraHtml.$html); */
        }
    }

    public function addRewardDetailsFront(\Magento\Framework\Event\Observer $observer) {
        
        $event = $observer->getEvent();
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $event->getLayout();
        $name = $event->getElementName();
        $block = $layout->getBlock($name);
        $transport = $event->getTransport();

        
        if ($block instanceof \Magento\Checkout\Block\Onepage && ($block->getNameInLayout() == 'checkout.root' || $event->getElementName() == 'checkout.root')) {
            $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Rewardcoupon');
            $block->setTemplate('onepage.phtml');
            $block->setNameInLayout("j2t_checkout_content");
            $extraHtml = $block->toHtml();
            $output = $transport->getData('output');

            $transport->setData('output', $output . $extraHtml);
        }
        
        if (!$this->_pointData->getActive()){
            return $this;
        }

        if (($block instanceof \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable) && ($block->getNameInLayout() == 'product.info.options.configurable' || $event->getElementName() == 'product.info.options.configurable')) {
            //options_configurable
            $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Product\View\Type\Configurable');

            $block->setTemplate('product/view/type/options/configurable.phtml');
            $block->setNameInLayout("point_info_details_configurable_js");
            $extraHtml = $block->toHtml();
            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        }

        if (($block instanceof \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle) && ($block->getNameInLayout() == 'product.info.bundle.options' || $event->getElementName() == 'product.info.bundle.options')) {
            //options_configurable
            $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Product\View\Type\Bundle');
            $block->setTemplate('product/view/type/options/bundle.phtml');
            $block->setNameInLayout("point_info_details_bundle_js");
            $extraHtml = $block->toHtml();
            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        }

        /*
         * customize.button
         * product.info.addto
         */
        if (($block instanceof \Magento\Catalog\Block\Product\View\Interceptor) && ($block->getNameInLayout() == 'product.info.addtocart.additional' || $event->getElementName() == 'product.info.addtocart.additional' || $block->getNameInLayout() == 'product.info.addto' || $event->getElementName() == 'product.info.addto' || $block->getNameInLayout() == 'product.info.addto.bundle' || $event->getElementName() == 'product.info.addto.bundle')) {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $show = $objectManager->get('J2t\Rewardpoints\Helper\Data')->showOnProductView();
            if ($show) {
                $block = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Pointinfo');
                $block->setData('product', $block->getProduct());
                $block->setData('from_list', false);

                $block->setTemplate('point_info.phtml');
                $extraHtml = $block->toHtml();

                $output = $transport->getData('output');
                $transport->setData('output', $extraHtml . $output);
            }
        }

        if (($block instanceof \Magento\Checkout\Block\Cart\Coupon) && ($block->getNameInLayout() == 'checkout.cart.coupon' || $event->getElementName() == 'checkout.cart.coupon')) {
            $html = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Rewardcoupon');
            $html->setTemplate('reward_coupon.phtml');
            $html->setNameInLayout("reward_coupon");

            $extraHtml = $html->toHtml();

            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        }

        if (($block instanceof \Magento\Framework\View\Element\AbstractBlock) && ($block->getNameInLayout() == 'customer_account_dashboard_top' || $event->getElementName() == 'customer_account_dashboard_top')) {
            
            $extraHtml = '...Point Dashboard...';
            $dashboardHtml = $event->getLayout()->createBlock('J2t\Rewardpoints\Block\Dashboard');
            $dashboardHtml->setTemplate('dashboard_points.phtml');
            $dashboardHtml->setNameInLayout("customer_account_points");

            $extraHtml = $dashboardHtml->toHtml();

            $output = $transport->getData('output');
            $transport->setData('output', $output . $extraHtml);
        }
    }

    public function recordPointsUponRegistration($observer) {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!$objectManager->get('J2t\Rewardpoints\Helper\Data')->getActive()){
            return $this;
        }
        
        $model = $objectManager->get('J2t\Rewardpoints\Model\Point')->loadByIncrementId(\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REGISTRATION, $customer->getId());
        $helper = $objectManager->get('J2t\Rewardpoints\Helper\Data');
        if (!$model->getId() && ($customerId = $customer->getId()) && ($points = $helper->getRegistrationPoints())) {
            $helper->recordPoints(\J2t\Rewardpoints\Model\Point::TYPE_POINTS_REGISTRATION, $customerId, $customer->getStoreId(), date('Y-m-d'), $points, 0, true, $helper->getPointsDelay(), $helper->getPointsDuration(), null, null);
        }
        
        return $this;
    }

    public function convertQuoteToOrderPoint($observer) {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $order->setRewardpointsDescription($quote->getRewardpointsDescription());
        $order->setRewardpointsQuantity($quote->getRewardpointsQuantity());
        $order->setBaseRewardpoints($quote->getBaseRewardpoints());
        $order->setRewardpoints($quote->getRewardpoints());
        $order->setRewardpointsReferrer($quote->getRewardpointsReferrer());
        $order->setRewardpointsGathered($quote->getRewardpointsGathered());
        $order->setRewardpointsCartRuleText($quote->getRewardpointsCartRuleText());

        return $this;
    }

    public function recordPointsAdminEvent($observer) {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        $request = $event->getRequest();

        if ($data = $request->getPost()) {

            if (isset($data['points_current'])) {
                if ($data['points_current'] > 0 || $data['points_current'] < 0) {
                    //$model = Mage::getModel('rewardpoints/stats');
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $model = $objectManager->create('J2t\Rewardpoints\Model\Point');
                    /*$inputFilter = new \Zend_Filter_Input(
                            ['date_start' => $this->_dateFilter, 'date_end' => $this->_dateFilter], [], $data
                    );
                    $data = $inputFilter->getUnescaped();*/
                    
                    $filterRules = [];
                    foreach (['date_start', 'date_end'] as $dateField) {
                        if (!empty($data[$dateField])) {
                            $filterRules[$dateField] = $this->_dateFilter;
                        }
                    }
                    $data = (new \Zend_Filter_Input($filterRules, [], $data))->getUnescaped();
                    

                    if (($points = trim($data['points_current'])) && $points < 0) {
                        $data['points_spent'] = $data['points_current'];
                        unset($data['points_current']);
                    }

                    $customer_data = $customer->getData();
                    $storeId = (isset($customer_data['store_id'])) ? $customer_data['store_id'] : 0;
                    ;

                    $ids = array();
                    if ($storeId) {
                        $data['store_id'] = $storeId;
                    } else {
                        foreach ($this->_storeManager->getStores() as $store) {
                            $ids[] = $store->getId();
                        }
                        $data['store_id'] = implode(",", $ids);
                    }

                    $data['customer_id'] = $customer_data['id'];
                    $data['order_id'] = \J2t\Rewardpoints\Model\Point::TYPE_POINTS_ADMIN;
                    //$model->loadPost($data);

                    $model->addData($data)->save();

                    //$model->save();

                    $description = $data['rewardpoints_description'];
                    if ($description == "") {
                        $description = __('Store input');
                    }

                    if (!empty($data['rewardpoints_notification'])) {
                        //TODO
                        //$model->sendAdminNotification($customer, $customer->getStoreId(), $data['points_current'], $description);
                    }
                }
            }
        }
    }

    public function saveOrderPoint($observer) {
        // Reindex quote ids
        $order = $observer->getEvent()->getOrder();
        //record points
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('J2t\Rewardpoints\Helper\Data');
        if (!$helper->getActive()){
            return $this;
        }
        if ($order->getCustomerId()) {
            $helper->recordPoints($order->getIncrementId(), $order->getCustomerId(), $order->getStoreId(), $order->getCreatedAt(), $order->getRewardpointsGathered(), $order->getRewardpointsQuantity(), true, $helper->getPointsDelay(), $helper->getPointsDuration(), $order->getState(), $order->getStatus());
            //processReferralTreatment / original method: sales_order_success_referral
            $helper->processReferralTreatment($order, $order->getQuote());
        }
    }

    public function verifyFielsetItems($observer) {
        $source = $observer->getEvent()->getSource();
        $target = $observer->getEvent()->getTarget();

        $fields = ["rewardpoints_gathered", "rewardpoints_gathered_float", "base_rewardpoints", "rewardpoints_used", "rewardpoints_catalog_rule_text"];
        $targetIsArray = is_array($target);
        $sourceIsArray = is_array($source);

        foreach ($fields as $code) {
            if ($sourceIsArray) {
                $value = isset($source[$code]) ? $source[$code] : null;
            } elseif ($source instanceof \Magento\Framework\DataObject) {
                //$value = $source->getDataUsingMethod($code);
                $value = $source->getData($code);
            }
            if ($targetIsArray) {
                $target[$code] = $value;
            } else {
                //$target->setDataUsingMethod($code, $value);
                $target->setData($code, $value);
            }
        }
    }

    public function salesEventConvertQuoteItemToOrderItem($observer) {
        $quoteItem = $observer->getEvent()->getItem();
        $orderItem = $observer->getEvent()->getOrderItem();
        $fields = ["rewardpoints_gathered", "rewardpoints_gathered_float", "base_rewardpoints", "rewardpoints_used", "rewardpoints_catalog_rule_text"];
        foreach ($fields as $code) {
            $orderItem->setData($code, $quoteItem->getData($code));
        }
        $observer->getEvent()->getOrderItem()->setRewardpointsGathered($observer->getEvent()->getItem()->getRewardpointsGathered());
        $observer->getEvent()->getOrderItem()->setRewardpointsGatheredFloat($observer->getEvent()->getItem()->getRewardpointsGatheredFloat());
        return $this;
    }

    public function salesEventConvertQuoteToOrder($observer) {
        $observer->getEvent()->getOrder()->setRewardpointsDescription($observer->getEvent()->getQuote()->getRewardpointsDescription());
        $observer->getEvent()->getOrder()->setRewardpointsQuantity($observer->getEvent()->getQuote()->getRewardpointsQuantity());
        $observer->getEvent()->getOrder()->setBaseRewardpoints($observer->getEvent()->getQuote()->getBaseRewardpoints());
        $observer->getEvent()->getOrder()->setRewardpoints($observer->getEvent()->getQuote()->getRewardpoints());
        $observer->getEvent()->getOrder()->setRewardpointsReferrer($observer->getEvent()->getQuote()->getRewardpointsReferrer());
        $observer->getEvent()->getOrder()->setRewardpointsGathered($observer->getEvent()->getQuote()->getRewardpointsGathered());
        $observer->getEvent()->getOrder()->setRewardpointsCartRuleText($observer->getEvent()->getQuote()->getRewardpointsCartRuleText());
        //TODO - verify it save is still necessary after beta to release candidate
        //$observer->getEvent()->getOrder()->save();
        return $this;
    }

}
