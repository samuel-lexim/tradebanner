<?php

namespace LitExtension\CustomerPassword\Model;

class AccountManagement extends \Magento\Customer\Model\AccountManagement
{
	
    protected function _beforeAuthenticate($email, $pass) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $configModel = $objectManager->create('Magento\Config\Model\Config');
        $cart_type = $configModel->getConfigDataValue('lecupd/general/type');
        if ($cart_type) {
            $model_name = "LitExtension\CustomerPassword\Model\Type\\" . ucfirst($cart_type);
            $model = $objectManager->create($model_name);
            if ($model) {
                $website_id = $objectManager->get('Magento\Store\Model\StoreManager')->getStore()->getWebsiteId();
                $customer = $objectManager->create('Magento\Customer\Model\Customer')->setWebsiteId($website_id)->loadByEmail($email);
                if ($customer) {
                    $a = $model->run($customer, $email, $pass);
                }
            }
        }
    }

    public function authenticate($username, $password) {
        $this->_beforeAuthenticate($username, $password);
        $customer = parent::authenticate($username, $password);
        return $customer;
    }

}