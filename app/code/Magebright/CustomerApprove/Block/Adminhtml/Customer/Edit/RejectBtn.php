<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 */

namespace Magebright\CustomerApprove\Block\Adminhtml\Customer\Edit;

use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ApproveButton
 */
class RejectBtn extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reject'),
            'on_click' => sprintf("location.href = '%s';", $this->getRejectUrl()),
            'class' => 'delete',
            'sort_order' => 140
        ];
    }

    /**
     * Get URL for approve button
     *
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl('magebright_customer_approve/customer/reject', ['id' => $this->getCustomerId()]);
    }
}
