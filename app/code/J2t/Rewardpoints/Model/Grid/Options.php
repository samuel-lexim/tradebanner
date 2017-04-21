<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Grid;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Backup\Helper\Data
     */
    protected $_model;

    /**
     * @param \Magento\Backup\Helper\Data $backupHelper
     */
    public function __construct(\J2t\Rewardpoints\Model\Cartpointrule $cartPointRule)
    {
        $this->_model = $cartPointRule;
    }

    /**
     * Return backup types array
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_model->ruleActionTypesToArray();
    }
}
