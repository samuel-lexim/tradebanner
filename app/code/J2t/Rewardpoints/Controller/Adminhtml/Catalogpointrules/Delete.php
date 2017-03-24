<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Adminhtml\Catalogpointrules;

use Magento\Framework\Model\Exception;

class Delete extends \J2t\Rewardpoints\Controller\Adminhtml\Catalogpointrules\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \Magento\CatalogRule\Model\Rule $model */
                $model = $this->_objectManager->create('J2t\Rewardpoints\Model\Catalogpointrule');
                $model->load($id);
                $model->delete();
                //$this->_objectManager->create('Magento\CatalogRule\Model\Flag')->loadSelf()->setState(1)->save();
                $this->messageManager->addSuccess(__('The rule has been deleted.'));
                $this->_redirect('rewardpoints_admin/*/');
                return;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('An error occurred while deleting the rule. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('rewardpoints_admin/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('Unable to find a rule to delete.'));
        $this->_redirect('rewardpoints_admin/*/');
    }
}
