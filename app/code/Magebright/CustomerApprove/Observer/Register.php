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
     * @var \Magebright\All\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magebright\All\Helper\Data $helper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magebright\All\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Register extension.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->helper->register('Magebright_CustomerApprove', '1.0.0', 'confirm');
        return $this;
    }
}
