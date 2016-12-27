<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 
 */

namespace Magebright\CustomerApprove\Model;

class Approve extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Approve status
     */
    const PENDING = 0;
    const APPROVED = 1;
    const REJECTED = 2;

    /**
     * Approve status customer attribute code.
     */
    const STATUS = 'approve_status';

    /**
     * @var \Magebright\CustomerApprove\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Magebright\CustomerApprove\Helper\Data $helper
     */
    public function __construct(
        \Magebright\CustomerApprove\Helper\Data $helper
    ){
        $this->helper = $helper;
    }
}
