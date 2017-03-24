<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block\Adminhtml\Cartpointrules\Quote\Edit\Tab;

class Labels extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Labels');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Labels');
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
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $rule = $rule = $this->_coreRegistry->registry('current_cart_point_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        
        
        // top shopping cart label
        $fieldset = $form->addFieldset('default_label_fieldset', ['legend' => __('Top Shopping Cart Label')]);
        $labels = $rule->getStoreLabels();

        $fieldset->addField(
            'store_default_label',
            'text',
            [
                'name' => 'store_labels[0]',
                'required' => false,
                'label' => __('Default Rule Top Cart Label for All Store Views'),
                'value' => isset($labels[0]) ? $labels[0] : ''
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset = $this->_createStoreSpecificFieldset($form, $labels, __('Store View Specific Top Shopping Cart Labels'));
        }

        if ($rule->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }
        
        // Shopping cart Summary Labels
        $fieldset = $form->addFieldset('default_summary_label_fieldset', ['legend' => __('Shopping Cart Summary Label')]);
        $labels = $rule->getStoreSummaryLabels();

        $fieldset->addField(
            'store_default_summary_label',
            'text',
            [
                'name' => 'store_labels_summary[0]',
                'required' => false,
                'label' => __('Default Rule Cart Summary Label for All Store Views'),
                'value' => isset($labels[0]) ? $labels[0] : ''
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset = $this->_createStoreSpecificFieldset($form, $labels, __('Store View Specific Shopping Cart Summary Labels'), '_summary');
        }

        if ($rule->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Create store specific fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param array $labels
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function _createStoreSpecificFieldset($form, $labels, $title = null, $name_extra = null)
    {
        $fieldset = $form->addFieldset(
            'store_labels'.$name_extra.'_fieldset',
            ['legend' => $title, 'class' => 'store-scope']
        );
        $renderer = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset');
        $fieldset->setRenderer($renderer);

        foreach ($this->_storeManager->getWebsites() as $website) {
            $fieldset->addField(
                "w_{$website->getId()}_label".$name_extra,
                'note',
                ['label' => $website->getName(), 'fieldset_html_class' => 'website']
            );
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField(
                    "sg_{$group->getId()}_label".$name_extra,
                    'note',
                    ['label' => $group->getName(), 'fieldset_html_class' => 'store-group']
                );
                foreach ($stores as $store) {
                    $fieldset->addField(
                        "s_{$store->getId()}".$name_extra,
                        'text',
                        [
                            'name' => 'store_labels'.$name_extra.'[' . $store->getId() . ']',
                            'required' => false,
                            'label' => $store->getName(),
                            'value' => isset($labels[$store->getId()]) ? $labels[$store->getId()] : '',
                            'fieldset_html_class' => 'store'
                        ]
                    );
                }
            }
        }
        return $fieldset;
    }
}
