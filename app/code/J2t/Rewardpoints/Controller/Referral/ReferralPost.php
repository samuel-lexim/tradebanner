<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Referral;

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReferralPost extends \Magento\Customer\Controller\AbstractAccount//\Magento\Customer\Controller\Account\Create //\Magento\Customer\Controller\Account
{
    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /** @var CustomerRepositoryInterface  */
    protected $customerRepository;

    /** @var CustomerDataBuilder */
    //protected $customerDataBuilder;

    /** @var FormKeyValidator */
    protected $formKeyValidator;

    /** @var CustomerExtractor */
    protected $customerExtractor;
    
    private $customerFactory, $referralFactory;
    
    protected $_storeManager;
    protected $_pointData = null;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param RedirectFactory $resultRedirectFactory
     * @param PageFactory $resultPageFactory
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerDataBuilder $customerDataBuilder
     * @param FormKeyValidator $formKeyValidator
     * @param CustomerExtractor $customerExtractor
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator,
        CustomerExtractor $customerExtractor,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \J2t\Rewardpoints\Helper\Data $pointHelper,
        \J2t\Rewardpoints\Model\ReferralFactory $referralFactory
	
    ) {
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        //$this->customerDataBuilder = $customerDataBuilder;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
        $this->customerFactory = $customerFactory;
        $this->referralFactory = $referralFactory;
        $this->_storeManager = $storeManager;
        $this->session = $customerSession;
        $this->_pointData = $pointHelper;
        parent::__construct($context);
    }

    
    /**
     * Change customer password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setPath('*/*/edit');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            //$customerId = $this->_getSession()->getCustomerId();
            $customerId = $this->session->getCustomerId();
            
            $emails           = $this->getRequest()->getPost('email');
            $names            = $this->getRequest()->getPost('name');
            
            try {
                //$current_customer = $this->customerRepository->getById($customerId);
                $current_customer = $this->customerFactory->create()->load($customerId);
                foreach ($emails as $key_email => $email){
                    $name = trim((string) $names[$key_email]);
                    $email = trim((string) $email);
                    
                    $no_errors = true;
                    if (!\Zend_Validate::is($email, 'EmailAddress')) {
                        $this->messageManager->addError(__('Wrong email address (%1).', $email));
                        $no_errors = false;
                    }
                    if ($name == ''){
                        $this->messageManager->addError(__('Friend name is required for email: %1 on line %2.', $email, ($key_email+1)));
                        $no_errors = false;
                    }
                    
                    if ($no_errors){
                        $referralModel = $this->referralFactory->create();
                        
                        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
                        $customer = $this->customerFactory->create();
                        if (isset($websiteId)) {
                            $customer->setWebsiteId($websiteId);
                        }

                        $customer->loadByEmail($email);
                        
                        $verifyStoreId = null;
                        if ($this->_pointData->isApplyStoreScope()){
                            $verifyStoreId = $this->_storeManager->getStore()->getId();
                        }
                        
                        if ($referralModel->isSubscribed($email, $verifyStoreId) || $customer->getEmail() == $email) {
                            $this->messageManager->addError(__('Email %1 has been already submitted.', $email));
                        } else {
                            if ($referralModel->subscribe($current_customer, $email, $name, false, $this->_storeManager->getStore()->getId())) {
                                $this->messageManager->addSuccess(__('Email %1 was successfully invited.', $email));
                            } else {
                                $this->messageManager->addError(__('There was a problem with the invitation email %1.', $email));
                            }
                        }
                    }
                }
            } catch (AuthenticationException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('There was a problem with the invitation.')
                );
            }
            
        }

        $resultRedirect->setPath('rewardpoints/referral');
        return $resultRedirect;
    }
}
