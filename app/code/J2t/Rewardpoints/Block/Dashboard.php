<?php

namespace J2t\Rewardpoints\Block;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Dashboard extends \Magento\Framework\View\Element\Template {

    protected $_template = 'dashboard_points.phtml';
    protected $_pointFactory;
    protected $_customerSession, $_customerRepository, $_customerAccountManagement;
    protected $_gathered_points = null;
    protected $_spent_points = null;
    protected $_expired_points = null;
    protected $_notyet_available_points = null;
    protected $_waiting_validation_points = null;
    protected $_pointData;

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \J2t\Rewardpoints\Model\PointFactory $pointFactory, CustomerRepositoryInterface $customerRepository, AccountManagementInterface $customerAccountManagement, \J2t\Rewardpoints\Helper\Data $pointHelper, array $data = []
    ) {
        $this->_pointData = $pointHelper;
        $this->_pointFactory = $pointFactory;
        $this->_customerSession = $customerSession;
        $this->_customerRepository = $customerRepository;
        $this->_customerAccountManagement = $customerAccountManagement;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    public function getPointsCurrent() {
        return $this->getGatheredPoints() - $this->getSpentPoints() - $this->getExpiredPoints();
    }

    public function getGatheredPoints() {
        if (!$this->_gathered_points) {
            $this->_gathered_points = $this->_pointData->getCustomerGatheredPoints($this->_customerSession->getId(), $this->_storeManager->getStore()->getId());
        }
        return $this->_gathered_points;
    }

    public function getSpentPoints() {
        if (!$this->_spent_points) {
            $this->_spent_points = $this->_pointData->getCustomerSpentPoints($this->_customerSession->getId(), $this->_storeManager->getStore()->getId());
        }

        return $this->_spent_points;
    }

    public function getExpiredPoints() {
        if (!$this->_expired_points) {
            $this->_expired_points = $this->_pointData->getCustomerExpiredPoints($this->_customerSession->getId(), $this->_storeManager->getStore()->getId());
        }
        return abs($this->_expired_points);
    }

    public function getNotAvailableYetPoints() {
        if (!$this->_notyet_available_points) {
            $this->_notyet_available_points = $this->_pointData->getCustomerNotAvailablePoints($this->_customerSession->getId(), $this->_storeManager->getStore()->getId());
        }
        return $this->_notyet_available_points;
    }

    public function getPointsWaitingValidation() {
        if (!$this->_waiting_validation_points) {
            $this->_waiting_validation_points = $this->_pointData->getCustomerWaitingValidationPoints($this->_customerSession->getId(), $this->_storeManager->getStore()->getId());
        }
        return $this->_waiting_validation_points;
    }

}
