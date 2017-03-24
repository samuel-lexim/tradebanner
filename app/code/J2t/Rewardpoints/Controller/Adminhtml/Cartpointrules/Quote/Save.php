<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Controller\Adminhtml\Cartpointrules\Quote;

class Save extends \J2t\Rewardpoints\Controller\Adminhtml\Cartpointrules\Quote
{
    /**
     * Promo quote save action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var $model \Magento\SalesRule\Model\Rule */
                $model = $this->_objectManager->create('J2t\Rewardpoints\Model\Cartpointrule');
                $this->_eventManager->dispatch(
                    'adminhtml_controller_rewardpoints_cart_prepare_save',
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
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong rule is specified.'));
                    }
                }

                $session = $this->_objectManager->get('Magento\Backend\Model\Session');

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('rewardpoints_admin/*/edit', ['id' => $model->getId()]);
                    return;
                }

                /*if (isset(
                    $data['simple_action']
                ) && $data['simple_action'] == 'by_percent' && isset(
                    $data['discount_amount']
                )
                ) {
                    $data['discount_amount'] = min(100, $data['discount_amount']);
                }*/
                
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                /*if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }*/
                
                if (isset($data['store_labels'])){
                    $data['labels'] = serialize($data['store_labels']);
                    unset($data['store_labels']);
                }
                
                if (isset($data['store_labels_summary'])){
                    $data['labels_summary'] = serialize($data['store_labels_summary']);
                    unset($data['store_labels_summary']);
                }
                
                $data['website_ids'] = implode(',',$data['website_ids']);
                $data['customer_group_ids'] = implode(',',$data['customer_group_ids']);
                
                //$data['store_labels'] = serialize($data['store_labels']);
                
                
                unset($data['rule']);
                
                /*echo "<pre>";
                print_r($data);
                die;*/
                
                
                $model->loadPost($data);
                
                /*$useAutoGeneration = (int)(!empty($data['use_auto_generation']));
                $model->setUseAutoGeneration($useAutoGeneration);*/

                $session->setPageData($model->getData());

                $model->save();
                $this->messageManager->addSuccess(__('The rule has been saved.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('rewardpoints_admin/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('rewardpoints_admin/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('rewardpoints_admin/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('rewardpoints_admin/*/new');
                }
                return;
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
