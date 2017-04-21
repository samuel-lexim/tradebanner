<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model;

/**
 * Customer group model
 *
 * @method \Magento\Customer\Model\Resource\Group _getResource()
 * @method \Magento\Customer\Model\Resource\Group getResource()
 * @method string getCustomerGroupCode()
 * @method \Magento\Customer\Model\Group setCustomerGroupCode(string $value)
 * @method \Magento\Customer\Model\Group setTaxClassId(int $value)
 * @method Group setTaxClassName(string $value)
 */
class Referral extends \Magento\Framework\Model\AbstractModel
{
    protected $_pointData;
    private $transportBuilder, $scopeConfig, $customerViewHelper, $customerRegistry;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \J2t\Rewardpoints\Helper\Data $pointHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_pointData = $pointHelper;
    }
    
    protected function _construct()
    {
        $this->_init('J2t\Rewardpoints\Model\Resource\Referral');
    }
    
    /*
     * public function getInvites($id){
        return $this->getCollection()->addClientFilter($id);
    }
     */
    
    public function loadByEmail($customerEmail)
    {
        $this->getResource()->loadByEmail($this, $customerEmail);
        return $this;
    }
    
    public function isConfirmed($email, $escape_status_verification = false, $storeId = null)
    {
        $collection = $this->getCollection();
        if (!$escape_status_verification){
            $collection->addFlagFilter(0);
        }
        $collection->addEmailFilter($email);
        
        if ($storeId){
            $collection->setStoreFilter($storeId);
        }
        
        return $collection->count() ? false : true;
    }
    
    public function isSubscribed($email, $storeId = null)
    {
        $collection = $this->getCollection()->addEmailFilter($email);
        if ($storeId){
            $collection->setStoreFilter($storeId);
        }
        return $collection->count() ? true : false;
    }
    
    public function loadByChildId($child_id)
    {
        $this->getResource()->loadByChildId($this, $child_id);
        return $this;
    }
    
    public function subscribe($parent, $email, $name, $voidEmailSubmit = false, $storeId = null)
    {
        $this->setRewardpointsReferralParentId($parent->getId())
                ->setRewardpointsReferralEmail($email)
                ->setRewardpointsReferralName($name)
                ->setRewardpointsReferralStatus("0")
                ->setStoreId($storeId);
        if ($voidEmailSubmit){
            return $this->save();
        }
        return $this->save() && $this->sendSubscription($parent, $email, $name);
    }
    
    /*protected function getFullCustomerObject($customer)
    {
        // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
        // object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, '\Magento\Customer\Api\Data\CustomerInterface');
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }*/
    
    
    public function sendConfirmation(
        $parent_customer,
        $child_customer,
        $destination,
        $destination_name,
        $storeId = null,
        $sendermailStoreId = null
    ) {
        
        $this->_pointData->sendConfirmation(
            $this,
            $parent_customer,
            $child_customer,
            $destination,
            $destination_name,
            $storeId,
            $sendermailStoreId
        );
        
        /*if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($parent_customer, $sendemailStoreId);
        }

        $store = $this->storeManager->getStore($parent_customer->getStoreId());
        $customerEmailData = $this->getFullCustomerObject($parent_customer);
        $sender  = $this->_scopeConfig->getValue(self::XML_PATH_CONFIRMATION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        $this->sendEmailTemplate(
            $destination,
            $destination_name,
            self::XML_PATH_CONFIRMATION_EMAIL_TEMPLATE,
            $sender,
            ['parent' => $customerEmailData, 'child' => $child_customer, 'referral' => $this, 'store' => $store],
            $storeId
        );*/

        return $this;
    }
    
    protected function sendSubscription(
        $parent_customer,
        $destination,
        $destination_name,
        $storeId = null,
        $sendemailStoreId = null
    ) {
        return $this->_pointData->sendSubscription(
            $this,
            $parent_customer,
            $destination,
            $destination_name,
            $storeId,
            $sendemailStoreId
        );
        /*if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($parent_customer, $sendemailStoreId);
        }

        $store = $this->storeManager->getStore($parent_customer->getStoreId());
        $customerEmailData = $this->getFullCustomerObject($parent_customer);
        
        if ($this->_scopeConfig->getValue(self::XML_PATH_SUBSCRIPTION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 'user-email-address') {
            $sender  = array(
                'name' => strip_tags($parent_customer->getFirstname().' '.$parent_customer->getLastname()),
                'email' => strip_tags($parent_customer->getEmail())
            );
        } else if ($this->_scopeConfig->getValue(self::XML_PATH_SUBSCRIPTION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) != 'user-email-address'){
            $sender  = $this->_scopeConfig->getValue(self::XML_PATH_SUBSCRIPTION_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } //else {
          //  $sender  = array(
          //      'name' => strip_tags($parent_customer->getFirstname().' '.$parent_customer->getLastname()),
          //      'email' => strip_tags($parent_customer->getEmail())
          //  );
        //}
        
        $real_url = $this->getUrl('rewardpoints/index/goReferral', array("referrer" => $parent_customer->getId()));
        $used_url = $this->getUrl('', array("referral-program" => str_replace('/','-',base64_encode($parent_customer->getId().'j2t'))));
        

        $this->sendEmailTemplate(
            $destination,
            $destination_name,
            self::XML_PATH_SUBSCRIPTION_EMAIL_TEMPLATE,
            $sender,
            ['parent' => $customerEmailData, 'referral' => $this, 'referral_url' => $used_url, 'store' => $store],
            $storeId
        );
         * 
         */

        return $this;
    }
    
    /*protected function sendEmailTemplate($email, $name, $template, $sender, $templateParams = [], $storeId = null)
    {
        $transport = $this->transportBuilder->setTemplateIdentifier(
            $this->scopeConfig->getValue($template, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            $this->scopeConfig->getValue($sender, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
        )->addTo(
            $email,
            $name
            //$customer->getEmail(),
            //$this->customerViewHelper->getCustomerName($customer)
        )->getTransport();
        $transport->sendMessage();

        return $this;
    }*/
}
