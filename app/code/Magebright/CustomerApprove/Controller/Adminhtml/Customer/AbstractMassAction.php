<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 */

namespace Magebright\CustomerApprove\Controller\Adminhtml\Customer;

use Magebright\CustomerApprove\Model\Approve;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magebright\CustomerApprove\Helper\Data as ApproveHelper;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;


/**
 * Class MassDelete
 */
abstract class AbstractMassAction extends \Magebright\CustomerApprove\Controller\Adminhtml\Customer
{
    /**
     * @var string
     */
    protected $redirectUrl = 'customer/index/';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor.
     *
     * @param Context                     $context
     * @param CustomerFactory             $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Approve                    $approve
     * @param ApproveHelper              $helper
     * @param CustomerExtensionFactory    $customerExtensionFactory
     * @param Filter                      $filter
     * @param CollectionFactory           $collectionFactory
     */
    public function __construct(
        Context $context,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        Approve $approve,
        ApproveHelper $helper,
        CustomerExtensionFactory $customerExtensionFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct(
            $context, $approve, $helper, $customerFactory, $customerRepository, $customerExtensionFactory
        );

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Return component referer url
     * TODO: Technical dept referer url should be implement as a part of Action configuration in in appropriate way
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl()?: 'customer/index/index';
    }

    /**
     * Execute action to collection items
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    abstract protected function massAction(AbstractCollection $collection);
}
