<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 
 */

namespace Magebright\CustomerApprove\Observer;

use Magento\Customer\Model\Session;
use Magebright\CustomerApprove\Helper\Data;
use Magebright\CustomerApprove\Model\Approve;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Framework\App\Response\Http\Interceptor as ResponseInterceptor;

class CheckCustomerApprove implements ObserverInterface
{
    /**
     * @var \Magebright\CustomerApprove\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var ResponseInterceptor
     */
    protected $responseInterceptor;

    /**
     * Constructor
     *
     * @param Session $customerSession
     * @param Data    $helper
     */
    public function __construct(
        Data $helper,
        Session $customerSession,
        CustomerFactory $customerFactory,
        ManagerInterface $messageManager,
        ResponseInterceptor $responseInterceptor
    ) {
        $this->helper = $helper;
        $this->session = $customerSession;
        $this->messageManager = $messageManager;
        $this->customerFactory = $customerFactory;
        $this->responseInterceptor = $responseInterceptor;
    }

    /**
     * Check customer approve status,
     * allow/block customer login and redirect accordingly.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return Magebright\CustomerApprove\Observer\CheckCustomerApprove
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $_customer = $observer->getEvent()->getCustomer();
        if(!$_customer || !$_customer->getId()) {
            return $this;
        }

        $customer = $this->customerFactory->create()->load($_customer->getId());
        if(!$customer || $customer->getApproveStatus() == Approve::APPROVED) {
            return $this;
        }

        $this->session->setCustomer($customer);
        $this->session->setId(null);
        $this->session->setCustomerGroupId(CustomerGroup::NOT_LOGGED_IN_ID);

        if($message = $this->helper->getUnapprovedCustomerMessage()) {
            $this->messageManager->addError(__($message));
        }

        $url = $this->helper->getRedirectUrl();
        $controller = $observer->getControllerAction();
        $this->responseInterceptor->setRedirect($url)->sendResponse();
        exit;
    }
}
