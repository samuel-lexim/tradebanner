<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 */

namespace Magebright\CustomerApprove\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Api\Data\CustomerExtensionFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const CONFIG_ENABLED = 'customer_approve/general/enabled';

    const CONFIG_AUTO_APPROVE = 'customer_approve/general/auto_approve';

    const CONFIG_NOTIFY_CUSTOMER = 'customer_approve/email/send_approve_email';

    const CONFIG_NOTIFY_ADMIN = 'customer_approve/admin_email_settings/notify_admin_after_account_creation';

    const CONFIG_NOTIFY_CUSTOMER_EMAIL_SENDER = 'customer_approve/email/customer_email_sender';

    const CONFIG_NOTIFY_ADMIN_EMAIL_SENDER = 'customer_approve/admin_email_settings/email_sender';

    const CONFIG_NOTIFY_CUSTOMER_APPROVED_EMAIL = 'customer_approve/email/approved_email_template';

    const CONFIG_NOTIFY_CUSTOMER_REJECTED_EMAIL = 'customer_approve/email/rejected_email_template';

    const CONFIG_NOTIFY_ADMIN_EMAIL_TEMPLATE = 'customer_approve/admin_email_settings/email_template';

    const CONFIG_NOTIFY_ADMIN_EMAIL = 'customer_approve/admin_email_settings/notify_admin_after_account_creation';

    const CONFIG_REDIRECT_CUSTOMER = 'customer_approve/redirect_settings/redirect_customers';

    const CONFIG_REDIRECT_TO_CMS_PAGE = 'customer_approve/redirect_settings/redirect_to_cms_page';

    const CONFIG_REDIRECT_TO_CUSTOM_URL = 'customer_approve/redirect_settings/custom_redirect_url';

    const CONFIG_UNAPPROVED_MESSAGE = 'customer_approve/redirect_settings/unapproved_customer_message';

    const CONFIG_ADMIN_NOTIFY_EMAIL_RECIPIENTS = 'customer_approve/admin_email_settings/recipients';

    const TYPE_ADMIN = 'admin';

    /**
     * Email sender config path.
     *
     * @var string
     */
    protected $senderEmailConfigKey = 'trans_email/ident_%s/email';
    protected $senderNameConfigKey = 'trans_email/ident_%s/name';

    /**
     * Currently selected store ID if applicable.
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerExtensionFactory
     */
    protected $customerExtensionFactory;

    /**
     * Constructor
     *
     * @param Context                     $context
     * @param TransportBuilder            $transportBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerExtensionFactory    $customerExtensionFactory
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        CustomerExtensionFactory $customerExtensionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->customerRepository = $customerRepository;
        $this->customerExtensionFactory = $customerExtensionFactory;

        parent::__construct($context);
    }

    /**
     * Return true if active and false otherwise.
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check whether auto approve enabled.
     *
     * @return bool
     */
    public function canAutoApprove($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_AUTO_APPROVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check whether to notify customer.
     *
     * @return bool
     */
    public function canNotifyCustomer($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_NOTIFY_CUSTOMER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check whether to notify admin.
     *
     * @return bool
     */
    public function canNotifyAdmin($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_NOTIFY_ADMIN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve store by id or current store if no id is passed.
     *
     * @return \Mage\Store\Model\Store
     */
    public function getStore($id = null)
    {
        return $this->storeManager->getStore($id);
    }

    /**
     * Retrieve after login redirect url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        if(false === $this->scopeConfig->isSetFlag(
            self::CONFIG_REDIRECT_CUSTOMER, ScopeInterface::SCOPE_STORE
        )) {
            return $this->_getRequest()->getServer('HTTP_REFERER');;
        }

        if ($url = $this->scopeConfig->getValue(
            self::CONFIG_REDIRECT_TO_CUSTOM_URL, ScopeInterface::SCOPE_STORE
        )) {
            return $url;
        }

        return $this->_getUrl(
            $this->scopeConfig->getValue(
                self::CONFIG_REDIRECT_TO_CMS_PAGE,
                ScopeInterface::SCOPE_STORE
            )
        );
    }

    /**
     * Retrieve approved email template id.
     *
     * @return string|int
     */
    public function getApprovedEmailTemplateId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_NOTIFY_CUSTOMER_APPROVED_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve rejected email template id.
     *
     * @return string|int
     */
    public function getRejectedEmailTemplateId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_NOTIFY_CUSTOMER_REJECTED_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve admin notification email template id.
     *
     * @return string|int
     */
    public function getAdminNotifyEmailTemplateId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_NOTIFY_ADMIN_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve un-approved customer message.
     *
     * @return string
     */
    public function getUnapprovedCustomerMessage()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_UNAPPROVED_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve sender email.
     *
     * @param string     $type
     * @param string|int $storeId
     *
     * @return string
     */
    public function getSenderEmail($type = null, $storeId = null)
    {
        if($type == self::TYPE_ADMIN) {
            $identifier = $this->scopeConfig->getValue(
                self::CONFIG_NOTIFY_ADMIN_EMAIL_SENDER,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        } else {
            $identifier = $this->scopeConfig->getValue(
                self::CONFIG_NOTIFY_CUSTOMER_EMAIL_SENDER,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        $key = sprintf($this->senderEmailConfigKey, $identifier);
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieve sender name.
     *
     * @param string     $type
     * @param string|int $storeId
     *
     * @return string
     */
    public function getSenderName($type = null, $storeId = null)
    {
        if($type == self::TYPE_ADMIN) {
            $identifier = $this->scopeConfig->getValue(
                self::CONFIG_NOTIFY_ADMIN_EMAIL_SENDER,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        } else {
            $identifier = $this->scopeConfig->getValue(
                self::CONFIG_NOTIFY_CUSTOMER_EMAIL_SENDER,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        $key = sprintf($this->senderNameConfigKey, $identifier);
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieve formatted sender details.
     *
     * @param string     $type
     * @param string|int $storeId
     *
     * @return string
     */
    public function getSender($type = null, $storeId = null)
    {
        $sender ['name'] = $this->getSenderName($type, $storeId);
        $sender ['email'] = $this->getSenderEmail($type, $storeId);

        return $sender;
    }

    /**
     * Retrieve admin email notification recipients.
     *
     * @return array
     */
    public function getAdminEmailRecipients($storeId = null)
    {
        $recipients = $this->scopeConfig->getValue(
            self::CONFIG_ADMIN_NOTIFY_EMAIL_RECIPIENTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if(!$recipients) {
            return false;
        }
        return explode(',', $recipients);
    }

    /**
     * Save approve status.
     *
     * @param $customer
     * @param $status
     *
     * @return void
     */
    public function saveApproveStatus($customer, $status)
    {
		$customerExtension = $customer->getExtensionAttributes();
       // if(null === $customerExtension) {
            $customerExtension = $this->customerExtensionFactory->create();
       // }

        $customerExtension->setApproveStatus($status);
        $customer->setExtensionAttributes($customerExtension);

        $this->customerRepository->save($customer);
    }

    /**
     * Retrieve customer full name.
     *
     * @param $customer
     *
     * @return string
     */
    public function getCustomerName($customer)
    {
        return trim($customer->getFirstname() . ' ' . $customer->getLastName());
    }

    /**
     * Send Email
     *
     * @param string $recipientName
     * @param string $recipientEmail
     * @param        $template
     * @param        $sender
     * @param array  $templateParams
     * @param null   $storeId
     *
     * @return $this
     *
     * @throws Exception
     */
    public function sendEmailTemplate(
        $recipientName, $recipientEmail, $template, $sender, $templateParams = [], $storeId = null
    ) {
        /** @var \Magento\Framework\Mail\Template\TransportBuilder $transport */
        $transport = $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            $sender
        )->addTo(
            $recipientEmail, $recipientName
        )->getTransport();

        $transport->sendMessage();

        return $this;
    }
   
}
