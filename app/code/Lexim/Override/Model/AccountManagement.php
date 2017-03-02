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

}