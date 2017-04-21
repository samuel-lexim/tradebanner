<?php
/**
 * Copyright © 2015 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Adminhtml\Catalogpointrules;

use Magento\Framework\Model\Exception;

class Save extends \J2t\Rewardpoints\Controller\Adminhtml\Catalogpointrules\Catalog
{
    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('J2t\Rewardpoints\Model\Catalogpointrule');
                $this->_eventManager->dispatch(
                    'adminhtml_controller_catalogpoint_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();
                /*$inputFilter = new \Zend_Filter_Input(
                    ['from_date' => $this->_dateFilter, 'to_date' => $this->_dateFilter],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();*/
                
                $filterRules = [];
                foreach (['from_date', 'to_date'] as $dateField) {
                    if (!empty($data[$dateField])) {
                        $filterRules[$dateField] = $this->_dateFilter;
                    }
                }
                $data = (new \Zend_Filter_Input($filterRules, [], $data))->getUnescaped();
                
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new Exception(__('Wrong rule specified.'));
                    }
                }

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('catalog_rule/*/edit', ['id' => $model->getId()]);
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);

                $model->loadPost($data);

                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());

                $model->save();

                $this->messageManager->addSuccess(__('The rule has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                /*if ($this->getRequest()->getParam('auto_apply')) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {*/
                    //$this->_objectManager->create('Magento\CatalogRule\Model\Flag')->loadSelf()->setState(1)->save();
                    $model->save();
                    //$this->messageManager->addSuccess(__('The rule has been saved.'));
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('rewardpoints_admin/*/edit', ['id' => $model->getId()]);
                        return;
                    }
                    $this->_redirect('rewardpoints_admin/*/');
                //}
                return;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('An error occurred while saving the rule data. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('rewardpoints_admin/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('rewardpoints_admin/*/');
    }
}
