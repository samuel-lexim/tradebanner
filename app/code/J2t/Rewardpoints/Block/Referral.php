<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Block;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer Referral list block
 */
class Referral extends \Magento\Customer\Block\Account\Dashboard // \Magento\Framework\View\Element\Template
{
    
    protected $_template = 'referral.phtml';
    /**
     * Customer Points collection
     *
     * @var \J2t\Rewardpoints\Model\Resource\Point\Collection
     */
    protected $_collection;

    /**
     * Review resource model
     *
     * @var \J2t\Rewardpoints\Model\Resource\Point\Collection\CollectionFactory
     */
    protected $_collectionFactory;
    //protected $_assetRepo;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer, $_customerSession, $_pointData, $_orderFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        //\Magento\Framework\View\Asset\Repository $assetRepo,
        AccountManagementInterface $customerAccountManagement,
        \J2t\Rewardpoints\Model\Resource\Referral\CollectionFactory $collectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \J2t\Rewardpoints\Helper\Data $pointHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_pointData = $pointHelper;
        $this->_orderFactory = $orderFactory;
        //$this->_assetRepo = $assetRepo;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
        $this->currentCustomer = $currentCustomer;
    }

    

    
    /**
     * Initialize review collection
     *
     * @return $this
     */
    protected function _initCollection()
    {
        $this->_collection = $this->_collectionFactory->create();
        if ($this->_pointData->isApplyStoreScope()){
            $this->_collection
                ->setStoreFilter($this->_storeManager->getStore()->getId());
        }
        $this->_collection
            //->setStoreFilter($this->_storeManager->getStore()->getId())
            ->addClientFilter($this->currentCustomer->getCustomerId());
            //->setDateOrder();
        return $this;
    }
    
    public function isUrlSecured()
    {
        return $this->_storeManager->getStore()->isCurrentlySecure();
    }
    
    public function getAddOneImageUrl()
    {
        return $this->_assetRepo->getUrl('J2t_Rewardpoints::images/j2t_add_one.png');
    }
    
    public function getMinusOneImageUrl()
    {
        return $this->_assetRepo->getUrl('J2t_Rewardpoints::images/j2t_minus_one.png');
    }

    /**
     * Gets collection items count
     *
     * @return int
     */
    public function count()
    {
        return $this->_getCollection()->getSize();
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'customer_points_list.toolbar'
        )->setCollection(
            $this->getCollection()
        );

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get collection
     *
     * @return \Magento\Review\Model\Resource\Review\Product\Collection
     */
    protected function _getCollection()
    {
        if (!$this->_collection) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    /**
     * Get collection
     *
     * @return \Magento\Review\Model\Resource\Review\Product\Collection
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    public function getReferringUrl()
    {
        $userId = $this->currentCustomer->getCustomerId();
        $real_url = $this->getUrl('rewardpoints/index/goReferral', array("referrer" => $userId));
        return $this->getUrl('', array("referral-program" => str_replace('/','-',base64_encode($userId.'j2t'))));
    }
    
    public function isPermanentLink()
    {
        return $this->_pointData->getReferralPerm();
    }
    
    public function getAddThisCode(){
        return $this->_pointData->getAddThisCode();
    }
    
    public function getAddThisAccount(){
        return $this->_pointData->getAddThisAccount();
    }
    
    public function isAddthis()
    {
        return $this->_pointData->canAddThis();
    }

    public function getReferrerPoints()
    {
        return $this->_pointData->getReferralPoint();
    }

    public function getFriendPoints()
    {
        return $this->_pointData->getReferralChildPoint();
    }
    
    public function getReferralCustomCode() {
        return $this->_pointData->getReferralCustomCode();
    }

    public function getReferrerRegistrationPoints()
    {
        return $this->_pointData->getReferrerRegistrationPoints();
    }

    public function getFriendRegistrationPoints()
    {
        return $this->_pointData->getFriendRegistrationPoints();
    }

    public function getReferrerCalculationType()
    {
        return $this->_pointData->getReferrerCalculationType();
    }

    public function getFriendCalculationType()
    {
        return $this->_pointData->getFriendCalculationType();
    }

    public function getMinOrderAmount()
    {
        return $this->_pointData->getMinOrderAmount();
    }
    
    public function getMinReferralOrderInCurrency($value) {
        return $this->_pointData->getMinReferralOrderInCurrency($value);
    }
    
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/', ['_secure' => true]);
    }
    
    /*public function getReviewLink()
    {
        return $this->getUrl('review/customer/view/');
    }*/

    
    /*public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }*/

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }

    /**
     * Add review summary
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        //$this->_getCollection()->load()->addReviewSummary();
        $this->_getCollection()->load();
        return parent::_beforeToHtml();
    }
        
}
