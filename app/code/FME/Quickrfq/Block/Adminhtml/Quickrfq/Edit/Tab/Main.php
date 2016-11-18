<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Quickrfq\Block\Adminhtml\Quickrfq\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('quickrfq');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('FME_Quickrfq::quickrfq')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rfq_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('RFQ Information')]);

        if ($model->getId()) {
            $fieldset->addField('quickrfq_id', 'hidden', ['name' => 'quickrfq_id']);
        }

        
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Change Status'),
                'title' => __('Change Status'),
                'name' => 'status',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
       // if (!$model->getId()) {
         //   $model->setData('is_active', $isElementDisabled ? '0' : '1');
       // }
       
       
        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
       
        $fieldset->addField(
            'create_date',
            'date',
            [
                'name' => 'create_date',
                'label' => __('Date Quote requested'),
                'title' => __('Date Quote requested'),
                'date_format' => $dateFormat,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from',  
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'update_date',
            'date',
            [
                'name' => 'update_date',
                'label' => __('Date Quote updated'),
                'title' => __('Date Quote updated'),
                'date_format' => $dateFormat,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from',
                'disabled' => $isElementDisabled                
            ]
        );
        
        $fieldset->addField(
            'company',
            'text',
            [
                'name' => 'company',
                'label' => __('Company'),
                'title' => __('Company'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'contact_name',
            'text',
            [
                'name' => 'contact_name',
                'label' => __('Contact Name'),
                'title' => __('Contact Name'),
                'class' => '',                
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('Phone'),
                'title' => __('Phone'),
                'class' => '',          
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'class' => '',          
                'disabled' => $isElementDisabled
            ]
        );       

        $fieldset->addField(
            'date',
            'date',
            [
                'name' => 'date',
                'label' => __('Date Quote Needed by Client'),
                'title' => __('Date Quote Needed by Client'),
                'date_format' => $dateFormat,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from',          
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'project_title',
            'text',
            [
                'name' => 'project_title',
                'label' => __('Project Title'),
                'title' => __('Project Title'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        
        $field = $fieldset->addField(
            'prd',
            'text',
            [
                'name' => 'prd',
                'label' => __('File'),
                'title' => __('File'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $renderer = $this->getLayout()->createBlock(
                'FME\Quickrfq\Block\Adminhtml\Quickrfq\Renderer\ElementFile'
        );
        $field->setRenderer($renderer);
            
            
        
        $fieldset->addField(
            'budget',
            'select',
            [
                'name' => 'budget',
                'label' => __('Budget Status'),
                'title' => __('Budget Status'),
                'options' => $model->getBudgetStatuses(),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        
        
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        
        $fieldset->addField(
            'overview',
            'textarea',
            [
                'name' => 'overview',
                'label' => __('Overview'),
                'title' => __('Overview'),                
                'class' => '',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
        
        
        
        /**
         * Check is single store mode
         */
    /*    if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        
        */

        

        $this->_eventManager->dispatch('adminhtml_quickrfq_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('RFQ Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('RFQ Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
