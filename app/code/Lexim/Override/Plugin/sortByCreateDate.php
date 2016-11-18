<?php

namespace Lexim\Override\Plugin;

class SortByCreateDate
{

    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $catalogConfig,
        $options
    ) {
        $options['created_at'] = __('Created Date');
        return $options;
    }
}