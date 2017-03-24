<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Referral;

class GoReferral extends \Magento\Framework\App\Action\Action
{
    
    protected $_customerSession;
    protected $resultForwardFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }
    
    
    public function execute()
    {
        
        $userId = (int) $this->getRequest()->getParam('referrer');
        if ($decrypt = $this->getRequest()->getParam('decrypt')){
            $userId = str_replace('j2t', '', base64_decode(trim(str_replace('-', '/', $decrypt))));
        } else if ($decrypt = $this->getRequest()->getParam('decript')){
            $userId = str_replace('j2t', '', base64_decode(trim(str_replace('-', '/', $decrypt))));
        }
        
        if ($userId){
            $session = $this->_objectManager->get('J2t\Rewardpoints\Model\Session');
            $session->setReferralUser($userId);
        }
        
        if ($url_redirection = $this->_objectManager->get('J2t\Rewardpoints\Helper\Data')->getReferralRedirection()){
            $this->_redirect($url_redirection);
        } else {
            /*$pageId = $this->_objectManager->get(
                'Magento\Framework\App\Config\ScopeConfigInterface'
            )->getValue(
                \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $resultPage = $this->_objectManager->get('Magento\Cms\Helper\Page')->prepareResultPage($this, $pageId);
            
            if (!$resultPage) {*/
                /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
                $resultForward = $this->resultForwardFactory->create();
                //$resultForward->forward('defaultIndex');
                $this->_redirect('/');
                //return $resultForward;
            /*}
            return $resultPage;*/
            
            
            ////////////
            
            /*$pageId = $this->_objectManager->get(
                'Magento\Framework\App\Config\ScopeConfigInterface'
            )->getValue(
                \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
                $this->_forward('defaultIndex');
            }*/
        }
    }
    
}
