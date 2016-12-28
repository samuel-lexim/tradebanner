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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Custom Estimate Information')]);

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
            'category',
            'select',
            [
                'name' => 'category',
                'label' => __('Category'),
                'title' => __('Category'),
                'options' => $model->getCategoryCustomEs(),
                'class' => '',          
                'disabled' => $isElementDisabled
            ]
        );

//        $fieldset->addField(
//            'date',
//            'date',
//            [
//                'name' => 'date',
//                'label' => __('Date Quote Needed by Client'),
//                'title' => __('Date Quote Needed by Client'),
//                'date_format' => $dateFormat,
//                'class' => 'validate-date validate-date-range date-range-custom_theme-from',
//                'disabled' => $isElementDisabled
//            ]
//        );

        $fieldset->addField(
            'material_01',
            'text',
            [
                'name' => 'material_01',
                'label' => __('Material 1'),
                'title' => __('Material 1'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'material_02',
            'text',
            [
                'name' => 'material_02',
                'label' => __('Material 2'),
                'title' => __('Material 2'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'material_03',
            'text',
            [
                'name' => 'material_03',
                'label' => __('Material 3'),
                'title' => __('Material 3'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'material_04',
            'text',
            [
                'name' => 'material_04',
                'label' => __('Material 4'),
                'title' => __('Material 4'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'material_05',
            'text',
            [
                'name' => 'material_05',
                'label' => __('Material 5'),
                'title' => __('Material 5'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'material_06',
            'text',
            [
                'name' => 'material_06',
                'label' => __('Material 6'),
                'title' => __('Material 6'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'standtype_options',
            'text',
            [
                'name' => 'standtype_options',
                'label' => __('Stand Type'),
                'title' => __('Stand Type'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'width',
            'text',
            [
                'name' => 'width',
                'label' => __('Width'),
                'title' => __('Width'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'height',
            'text',
            [
                'name' => 'height',
                'label' => __('Height'),
                'title' => __('Height'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'quantity',
            'text',
            [
                'name' => 'quantity',
                'label' => __('Quantity'),
                'title' => __('Quantity'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'delivery',
            'text',
            [
                'name' => 'delivery',
                'label' => __('Delivery'),
                'title' => __('Delivery'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'company_name_02',
            'text',
            [
                'name' => 'company_name_02',
                'label' => __('Company-Name'),
                'title' => __('Company-Name'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'first_name',
            'text',
            [
                'name' => 'first_name',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'last_name',
            'text',
            [
                'name' => 'last_name',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'street_address',
            'text',
            [
                'name' => 'street_address',
                'label' => __('Street Address'),
                'title' => __('Street Address'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'address_line_02',
            'text',
            [
                'name' => 'address_line_02',
                'label' => __('Address Line 2'),
                'title' => __('Address Line 2'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'state_options',
            'text',
            [
                'name' => 'state_options',
                'label' => __('State'),
                'title' => __('State'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'zip_code',
            'text',
            [
                'name' => 'zip_code',
                'label' => __('Zip Code'),
                'title' => __('Zip Code'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'finishing_options_mc01',
            'text',
            [
                'name' => 'finishing_options_mc01',
                'label' => __('Finishing Options Multi 1'),
                'title' => __('Finishing Options Multi 1'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'finishing_options_mc02',
            'text',
            [
                'name' => 'finishing_options_mc02',
                'label' => __('Finishing Options Multi 2'),
                'title' => __('Finishing Options Multi 2'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'finishing_options_mc03',
            'text',
            [
                'name' => 'finishing_options_mc03',
                'label' => __('Finishing Options Multi 3'),
                'title' => __('Finishing Options Multi 3'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'finishing_options_mc04',
            'text',
            [
                'name' => 'finishing_options_mc04',
                'label' => __('Finishing Options Multi 4'),
                'title' => __('Finishing Options Multi 4'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'finishing_options',
            'text',
            [
                'name' => 'finishing_options',
                'label' => __('Finishing Options'),
                'title' => __('Finishing Options'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'lamination_options',
            'text',
            [
                'name' => 'lamination_options',
                'label' => __('Lamination'),
                'title' => __('Lamination'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'lamination_options_mc01',
            'text',
            [
                'name' => 'lamination_options_mc01',
                'label' => __('Lamination Multi Choice 1'),
                'title' => __('Lamination Multi Choice 1'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'lamination_options_mc02',
            'text',
            [
                'name' => 'lamination_options_mc02',
                'label' => __('Lamination Multi Choice 2'),
                'title' => __('Lamination Multi Choice 2'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'lamination_options_mc03',
            'text',
            [
                'name' => 'lamination_options_mc03',
                'label' => __('Lamination Multi Choice 3'),
                'title' => __('Lamination Multi Choice 3'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'round_corners_options',
            'text',
            [
                'name' => 'round_corners_options',
                'label' => __('Round Corners'),
                'title' => __('Round Corners'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'color_options',
            'text',
            [
                'name' => 'color_options',
                'label' => __('Color'),
                'title' => __('Color'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'color_options_02',
            'text',
            [
                'name' => 'color_options_02',
                'label' => __('Color 2'),
                'title' => __('Color 2'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'diecut_options',
            'text',
            [
                'name' => 'diecut_options',
                'label' => __('Die Cut'),
                'title' => __('Die Cut'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'frame_options',
            'text',
            [
                'name' => 'frame_options',
                'label' => __('Frame'),
                'title' => __('Frame'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'grommet_options',
            'text',
            [
                'name' => 'grommet_options',
                'label' => __('Gromets'),
                'title' => __('Gromets'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'hstake_options',
            'text',
            [
                'name' => 'hstake_options',
                'label' => __('H-Stakes'),
                'title' => __('H-Stakes'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'thickness_options',
            'text',
            [
                'name' => 'thickness_options',
                'label' => __('Thickness'),
                'title' => __('Thickness'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'thickness_options_02',
            'text',
            [
                'name' => 'thickness_options_02',
                'label' => __('Thickness 2'),
                'title' => __('Thickness 2'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'thickness_options_03',
            'text',
            [
                'name' => 'thickness_options_03',
                'label' => __('Thickness 3'),
                'title' => __('Thickness 3'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'thickness_options_04',
            'text',
            [
                'name' => 'thickness_options_04',
                'label' => __('Thickness 4'),
                'title' => __('Thickness 4'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'size_options',
            'text',
            [
                'name' => 'size_options',
                'label' => __('Size'),
                'title' => __('Size'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'size_options_02',
            'text',
            [
                'name' => 'size_options_02',
                'label' => __('Size 2'),
                'title' => __('Size 2'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'carmodel',
            'text',
            [
                'name' => 'carmodel',
                'label' => __('Car Model'),
                'title' => __('Car Model'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'carwrapping',
            'text',
            [
                'name' => 'carwrapping',
                'label' => __('Car Wrapping'),
                'title' => __('Car Wrapping'),
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

        
//        $fieldset->addField(
//            'budget',
//            'select',
//            [
//                'name' => 'budget',
//                'label' => __('Budget Status'),
//                'title' => __('Budget Status'),
//                'options' => $model->getBudgetStatuses(),
//                'class' => '',
//                'disabled' => $isElementDisabled
//            ]
//        );
        
        
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
        return __('Custom Estimate Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Custom Estimate Information');
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
