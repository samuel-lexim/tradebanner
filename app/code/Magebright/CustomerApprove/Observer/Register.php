<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 
 */

namespace Magebright\CustomerApprove\Observer;

use Magento\Framework\Event\ObserverInterface;

class Register implements ObserverInterface
{
    /**
     * @var \Magebright\CustomerApprove\Helper\Data
     */
    protected $helper;

    /**
     * Register constructor.
     * @param \Magebright\CustomerApprove\Helper\Data $helper
     */
    public function __construct(
        \Magebright\CustomerApprove\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Register extension.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->helper->register('Magebright_CustomerApprove', '1.0.0', 'confirm');
        return $this;
    }
}
