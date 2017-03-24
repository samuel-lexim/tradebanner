<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Config;

class Mapfields extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

    protected $_templateRenderer;
    protected $_senderRenderer;

    protected function _getSenderRenderer() {
        if (!$this->_senderRenderer) {
            $this->_senderRenderer = $this->getLayout()->createBlock(
                    'J2t\Rewardpoints\Block\Config\Selectsender', '', ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_senderRenderer->setClass('notification_point_sender_select');
        }
        return $this->_senderRenderer;
    }

    protected function _getTemplateRenderer() {
        if (!$this->_templateRenderer) {
            $this->_templateRenderer = $this->getLayout()->createBlock(
                    '\J2t\Rewardpoints\Block\Config\Selecttemplate', '', ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_templateRenderer->setClass('notification_point_template_select');
        }
        return $this->_templateRenderer;
    }

    protected function _prepareToRender() {
        $this->addColumn('min_value', [
            'label' => __('Min value'),
            'style' => 'width:90px',
            'class' => 'validate-zero-or-greater',
        ]);
        $this->addColumn('max_value', [
            'label' => __('Max value'),
            'style' => 'width:90px',
            'class' => 'validate-zero-or-greater',
        ]);
        $this->addColumn('duration', [
            'label' => __('Duration (in days)'),
            'style' => 'width:90px',
            'class' => 'validate-zero-or-greater',
        ]);

        $this->addColumn('sender', [
            'label' => __('Email sender'),
            'style' => 'width:90px',
            'renderer' => $this->_getSenderRenderer(),
        ]);

        $this->addColumn('template', [
            'label' => __('Email template'),
            'style' => 'width:90px',
            'renderer' => $this->_getTemplateRenderer(),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Custom Point Value');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\Object $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {
        /*$row->setData(
                'option_extra_attr_' . $this->_getSenderRenderer()->calcOptionHash($row->getData('sender')), 'selected="selected"'
        );
        $row->setData(
                'option_extra_attr_' . $this->_getTemplateRenderer()->calcOptionHash($row->getData('template')), 'selected="selected"'
        );*/
        
        $optionExtraAttr['option_' . $this->_getSenderRenderer()->calcOptionHash($row->getData('sender'))] = 'selected="selected"';
        /*$row->setData(
                'option_extra_attrs', $optionExtraAttr
        );*/
        
        $optionExtraAttr['option_' . $this->_getTemplateRenderer()->calcOptionHash($row->getData('template'))] = 'selected="selected"';
        $row->setData(
                'option_extra_attrs', $optionExtraAttr
        );
        
        
    }

}
