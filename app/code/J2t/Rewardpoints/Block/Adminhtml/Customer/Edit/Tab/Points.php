<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer account form block
 */
class Points extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface {

    protected $_module = 'rewardpoints_admin';

    /**
     * @var string
     */
    //protected $_template = 'tab/newsletter.phtml';
    protected $_template = 'customer/tab/points.phtml';

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;
    protected $customerRepository;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $_pointData = null;
    protected $_storeModel = null;
    protected $_storeManager = null;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param AccountManagementInterface $customerAccountManagement
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory, AccountManagementInterface $customerAccountManagement, CustomerRepositoryInterface $customerRepository, \J2t\Rewardpoints\Helper\Data $pointHelper, \Magento\Store\Model\System\Store $store,
    //\Magento\Store\Model\StoreManagerInterface $storeManager,
            array $data = []
    ) {
        $this->_pointData = $pointHelper;
        $this->_storeModel = $store;
        //$this->_storeManager = $storeManager;
        $this->_storeManager = $context->getStoreManager();
        $this->_subscriberFactory = $subscriberFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel() {
        return __('Reward Points');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle() {
        return __('Reward Points');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass() {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl() {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded() {
        return false;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab() {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }

    /**
     * Initialize the form.
     *
     * @return $this
     */
    public function initForm() {
        if (!$this->canShowTab()) {
            return $this;
        }
        /*         * @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_rewardpoints');
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        //$subscriber = $this->_subscriberFactory->create()->loadByCustomerId($customerId);
        //$this->_coreRegistry->register('subscriber', $subscriber);
        //$customer = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER);
        $customer = $this->customerRepository->getById($customerId);

        $pointInfo = [];
        if ($this->_pointData->isApplyStoreScope($customer->getStoreId())) {
            foreach ($this->_storeManager->getStores() as $store) {
                $storeId = $store->getId();
                $storeName = $this->_storeModel->getStoreName($storeId);
                $currentPoints = $this->_pointData->getCurrentCustomerPoints($customerId, $storeId);
                $pointInfo[] = __('%1 points available on %2', $currentPoints, $storeName);
            }
        } else {
            $currentPoints = $this->_pointData->getCurrentCustomerPoints($customerId, $customer->getStoreId());
            $pointInfo[] = __('%1 points available', $currentPoints);
        }

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('User Points (%1)', implode(' / ', $pointInfo))]);

        $fieldset->addField(
                'points_current', 'text', [
            'label' => __('Points'),
            'name' => 'points_current',
            'class' => 'validate-number',
            'data-form-part' => $this->getData('target_form'),
            'note' => __('Use negative value in order to remove points.')
                ]
        );

        /* $fieldset->addField(
          'points_spent',
          'text',
          [
          'label' => __('Points Spent'),
          'name' => 'points_spent',
          'class'     => 'validate-greater-than-zero',
          'data-form-part' => $this->getData('target_form')
          ]
          ); */

        $fieldset->addField(
                'rewardpoints_description', 'text', [
            'label' => __('Description'),
            'name' => 'rewardpoints_description',
            'class' => '',
            'data-form-part' => $this->getData('target_form'),
            'note' => __('Description will be shown within user account as well as on notification email.')
                ]
        );


        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);


        /* '(function( $ ) {
          $("#_rewardpointsdate_start").calendar({
          dateFormat: "'.$dateFormat.'",
          showsTime: true,
          timeFormat: "h:mm a",
          buttonImage: "'.$this->getViewFileUrl('images/grid-cal.png').'",
          buttonText: "Select Date"
          })
          })(jQuery)' */

        $fieldset->addField(
                'date_start', 'date', [
            'name' => 'date_start',
            'label' => __('From Date'),
            'title' => __('From Date'),
            'image' => $this->getViewFileUrl('images/grid-cal.png'),
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat,
            'data-form-part' => $this->getData('target_form'),
            'after_element_html' => '<script>(function( $ ) {
					$("#_rewardpointsdate_start").calendar({
					   dateFormat: "' . $dateFormat . '",
					   showsTime: false,
					   buttonImage: "' . $this->getViewFileUrl('images/grid-cal.png') . '",
					   buttonText: "Select Date"
					});
				 })(jQuery)</script>'
                ]
        );
        $fieldset->addField(
                'date_end', 'date', [
            'name' => 'date_end',
            'label' => __('To Date'),
            'title' => __('To Date'),
            'image' => $this->getViewFileUrl('images/grid-cal.png'),
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat,
            'data-form-part' => $this->getData('target_form'),
            'after_element_html' => '<script>(function( $ ) {
					$("#_rewardpointsdate_end").calendar({
					   dateFormat: "' . $dateFormat . '",
					   showsTime: false,
					   buttonImage: "' . $this->getViewFileUrl('images/grid-cal.png') . '",
					   buttonText: "Select Date"
					});
				 })(jQuery)</script>'
                ]
        );

        $fieldset->addField(
                'rewardpoints_notification', 'checkbox', [
            'label' => __('Send Notification Email'),
            'name' => 'rewardpoints_notification',
            'class' => 'validate-greater-than-zero',
            'onclick' => 'this.value = this.checked ? 1 : 0;',
            'data-form-part' => $this->getData('target_form')
                ]
        );

        /* if ($this->customerAccountManagement->isReadOnly($customerId)) {
          $form->getElement('subscription')->setReadonly(true, true);
          } */

        //$form->getElement('subscription')->setIsChecked($subscriber->isSubscribed());

        /* $changedDate = $this->getStatusChangedDate();
          if ($changedDate) {
          $fieldset->addField(
          'change_status_date',
          'label',
          [
          'label' => $subscriber->isSubscribed() ? __('Last Date Subscribed') : __('Last Date Unsubscribed'),
          'value' => $changedDate,
          'bold' => true
          ]
          );
          } */

        $this->setForm($form);
        return $this;
    }

    /**
     * Retrieve the date when the subscriber status changed.
     *
     * @return null|string
     */
    /* public function getStatusChangedDate()
      {
      $subscriber = $this->_coreRegistry->registry('subscriber');
      if ($subscriber->getChangeStatusAt()) {
      return $this->formatDate(
      $subscriber->getChangeStatusAt(),
      \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM,
      true
      );
      }

      return null;
      } */

    /**
     * Prepare the layout.
     *
     * @return $this
     */
    protected function _prepareLayout() {
        $this->setChild(
                'grid',
                /* $this->getLayout()->createBlock(
                  'Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid',
                  'newsletter.grid'
                  ) */ $this->getLayout()->createBlock(
                        'J2t\Rewardpoints\Block\Adminhtml\Customer\Edit\Tab\Points\Grid', 'rewardpoints.grid'
                )
        );
        parent::_prepareLayout();
        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml() {
        if ($this->canShowTab()) {
            $this->initForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }

}
