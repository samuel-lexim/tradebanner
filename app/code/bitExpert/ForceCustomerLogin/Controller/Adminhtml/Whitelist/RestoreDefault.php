<?php

/*
 * This file is part of the Force Login module for Magento2.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bitExpert\ForceCustomerLogin\Controller\Adminhtml\Whitelist;

use \bitExpert\ForceCustomerLogin\Api\Repository\WhitelistRepositoryInterface;
use \Magento\Framework\Controller\Result\RedirectFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Message\ManagerInterface;
use \bitExpert\ForceCustomerLogin\Api\Data\WhitelistEntryFactoryInterface;

/**
 * Class RestoreDefault
 * @package bitExpert\ForceCustomerLogin\Controller\Adminhtml\Whitelist
 * @codingStandardsIgnoreFile
 */
class RestoreDefault extends \Magento\Framework\App\Action\Action
{
    /**
     * @var WhitelistEntryFactoryInterface
     */
    protected $whitelistEntityFactory;
    /**
     * @var WhitelistRepositoryInterface
     */
    protected $whitelistRepository;
    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var array Default routes
     */
    protected $defaultRoutes;

    /**
     * Save constructor.
     * @param WhitelistEntryFactoryInterface $whitelistEntityFactory
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param Context $context
     * @param array $defaultRoutes
     */
    public function __construct(
        WhitelistEntryFactoryInterface $whitelistEntityFactory,
        WhitelistRepositoryInterface $whitelistRepository,
        Context $context,
        array $defaultRoutes
    ) {
        $this->whitelistEntityFactory = $whitelistEntityFactory;
        $this->whitelistRepository = $whitelistRepository;
        $this->redirectFactory = $context->getResultRedirectFactory();
        $this->messageManager = $context->getMessageManager();
        $this->context = $context;
        $this->defaultRoutes = $defaultRoutes;
        parent::__construct($context);
    }

    /**
     * Restore whitelist defaults action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $result = $this->redirectFactory->create();
        $result->setHttpResponseCode(200);
        $result->setPath('ForceCustomerLogin/Whitelist/index');

        $whiteLists = $this->getWhiteListEntriesIndexedByPath();

        try {
            foreach ($this->defaultRoutes as $route => $description) {
                if (\array_key_exists($route, $whiteLists)) {
                    continue;
                }

                $this->whitelistRepository->createEntry(null, $description, $route);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __("Could not restore default whitelist!")
            );
            return $result;
        }

        $this->messageManager->addSuccessMessage(
            __("Successfully restored whitelist defaults.")
        );

        return $result;
    }

    /**
     * Get all current whitelists indexed by it's url rule
     *
     * @return array
     */
    protected function getWhiteListEntriesIndexedByPath()
    {
        $whiteListCollection = $this->whitelistRepository->getCollection();
        $whiteLists = [];

        foreach ($whiteListCollection->getItems() as $whiteList) {

            $whiteLists[$whiteList->getData(\bitExpert\ForceCustomerLogin\Model\WhitelistEntry::KEY_URL_RULE)] =
                $whiteList->getData(\bitExpert\ForceCustomerLogin\Model\WhitelistEntry::KEY_LABEL);
        }

        return $whiteLists;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('bitExpert_ForceCustomerLogin::bitexpert_force_customer_login_manage');
    }
}
