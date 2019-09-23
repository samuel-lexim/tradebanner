<?php
/**
 * @project: CustomerPassword
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

namespace LitExtension\CustomerPassword\Model;

class Type
{
    public function run($customerModel, $email, $password){
        if($customer_id = $customerModel->getId()){
            $pw_hash = $customerModel->getPasswordHash();
            if(!$pw_hash){
                return false;
            }
            $check = $this->validatePassword($customerModel, $email, $password, $pw_hash);
            if($check){
                $customerModel->setPassword($password);
                try{
                    $customerModel->save();
                }catch (Exception $e){}
                return true;
            }
        }
        return false;
    }

    public function validatePassword($customerModel, $email, $password, $pw_hash){
        return false;
    }
}