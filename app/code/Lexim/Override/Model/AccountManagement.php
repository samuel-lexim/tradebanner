<?php

namespace Lexim\Override\Model;
use \Magento\Customer\Api\Data\CustomerInterface;

class AccountManagement extends \Magento\Customer\Model\AccountManagement {

    public function createAccount(CustomerInterface $customer, $password = null, $redirectUrl = '')
    {
        if ($password !== null) {
            //$this->checkPasswordStrength($password);
            $hash = $this->createPasswordHash($password);
        } else {
            $hash = null;
        }
        return $this->createAccountWithPasswordHash($customer, $hash, $redirectUrl);
    }

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