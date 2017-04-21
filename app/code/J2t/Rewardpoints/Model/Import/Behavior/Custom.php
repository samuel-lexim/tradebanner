<?php
/**
 * Copyright © 2016 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Import\Behavior;

/**
 * Import behavior source model
 */
class Custom extends \Magento\ImportExport\Model\Source\Import\AbstractBehavior
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        /*return [
            \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE => __('Add/Update Complex Data'),
            \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE => __('Delete Entities'),
            \Magento\ImportExport\Model\Import::BEHAVIOR_CUSTOM => __('Custom Action')
        ];*/
        return [
            \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND => __('Add')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'rewardpoints_account';
    }
}
