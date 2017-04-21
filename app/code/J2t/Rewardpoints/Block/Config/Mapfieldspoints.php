<?php

/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Block\Config;

class Mapfieldspoints extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

    protected $_customGroup, $_date;

    protected function _getGroupRenderer() {
        if (!$this->_customGroup) {
            $this->_customGroup = $this->getLayout()->createBlock(
                    '\J2t\Rewardpoints\Block\Config\Selectgroup', '', ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_customGroup->setClass('custom_point_group_select');

            $this->_customGroup->setIsMultiSelect(1);
            $this->_customGroup->setExtraParams('multiple="multiple" size="5" style="width:90px"');
        }
        return $this->_customGroup;
    }

    protected function _getDateRenderer($id) {
        //if (!$this->_date) {
        $this->_date = $this->getLayout()->createBlock(
                '\J2t\Rewardpoints\Block\Config\Date', '', ['data' => ['is_render_to_js_template' => true]]
        );
        $this->_date->setClass('custom_point_date_input');

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $this->_date->setImage($this->getViewFileUrl('images/grid-cal.png'));
        $this->_date->setId($id);
        $this->_date->setDateFormat($dateFormat);
        $this->_date->setExtraParams('style="width:110px"');
        $this->_date->toHtml();
        //}
        return $this->_date;
    }

    protected function _prepareToRender() {
        $this->addColumn('min_cart_value', [
            'label' => __('Min Cart'),
            'style' => 'width:50px',
            'class' => 'validate-zero-or-greater',
        ]);
        $this->addColumn('max_cart_value', [
            'label' => __('Max Cart'),
            'style' => 'width:50px',
            'class' => 'validate-zero-or-greater',
        ]);
        $this->addColumn('point_value', [
            'label' => __('Value'),
            'style' => 'width:50px',
            'class' => 'validate-zero-or-greater',
        ]);
        /* $this->addColumn('group_id', [
          'label' => __('Customer Group'),
          'style' => 'width:50px',
          'class' => 'validate-zero-or-greater',
          ]); */
        $this->addColumn('date_from', [
            'label' => __('From'),
            'style' => 'width:50px',
            'renderer' => $this->_getDateRenderer('date_from'),
        ]);

        $this->addColumn('date_end', [
            'label' => __('Until'),
            'style' => 'width:50px',
            'renderer' => $this->_getDateRenderer('date_end'),
        ]);


        $this->addColumn(
                'group_id', ['label' => __('Group'), 'renderer' => $this->_getGroupRenderer()]
        );
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
        
        if (is_array($row->getData('group_id')) && sizeof($row->getData('group_id'))) {
            $optionExtraAttr = [];
            foreach ($row->getData('group_id') as $value) {
                $optionExtraAttr['option_' . $this->_getGroupRenderer()->calcOptionHash($value)] = 'selected="selected"';
                $row->setData(
                        'option_extra_attrs', $optionExtraAttr
                );
            }
        }
    }

    public function renderCellTemplate($columnName) {
        $inputName = $this->_getCellInputElementName($columnName);
        if ($columnName == "date_from" || $columnName == "date_end") {
            $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
            return $this->_getDateRenderer($columnName)
                            ->setName($inputName)
                            ->setTitle($columnName)
                            ->setImage($this->getViewFileUrl('images/grid-cal.png'))
                            ->setFormat($dateFormat)
                            //->setValue('#{'.$columnName.'}')
                            //->setValue($columnName)
                            ->setValue('<%- ' . $columnName . ' %>')
                            ->setExtraParams('style="width:110px"')
                            ->toHtml();
        }

        if ($columnName == "group_id") {
            return $this->_getGroupRenderer()
                            ->setName($inputName . '[]')
                            ->toHtml();
        }


        return parent::renderCellTemplate($columnName);
    }

}
