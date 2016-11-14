<?php

namespace Lexim\Override\Model;

class CatalogModelConfigSourceListSort extends \Magento\Catalog\Model\Config\Source\ListSort {

    public function toOptionArray()
    {
        $options = [];
        $options[] = ['label' => __('Position'), 'value' => 'position'];
        $options[] = ['label' => __('Create Date'), 'value' => 'created_at'];
        foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
            $options[] = ['label' => __($attribute['frontend_label']), 'value' => $attribute['attribute_code']];
        }
        return $options;
    }
}