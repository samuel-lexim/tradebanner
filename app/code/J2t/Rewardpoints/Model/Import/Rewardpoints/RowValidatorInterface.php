<?php
/**
 * Copyright © 2016 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace J2t\Rewardpoints\Model\Import\Rewardpoints;

interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
    //const ERROR_INVALID_POINTS= 'InvalidValueTITLE';
    const ERROR_POINTS_IS_EMPTY = 'EmptyPOINTS';
    const ERROR_EMAIL_IS_EMPTY = 'EmptyEMAIL';
    const ERROR_CUSTOMER_IS_EMPTY = 'EmptyCUSTOMER';
    /**
     * Initialize validator
     *
     * @return $this
     */
    public function init($context);
}
