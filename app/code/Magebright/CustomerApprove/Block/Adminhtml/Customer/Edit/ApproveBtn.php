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
class ApproveBtn extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Approve'),
            'on_click' => sprintf("location.href = '%s';", $this->getApproveUrl()),
            'class' => 'save',
            'sort_order' => 130
        ];
    }

    /**
     * Get URL for approve button
     *
     * @return string
     */
    public function getApproveUrl()
    {
        return $this->getUrl('magebright_customer_approve/customer/approved', ['id' => $this->getCustomerId()]);
    }
}
