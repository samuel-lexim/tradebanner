<?php
namespace FME\Quickrfq\Controller\Index;
use Magento\Framework\App\Filesystem\DirectoryList;


class Post extends \FME\Quickrfq\Controller\Index
{
        
        const CONFIG_CAPTCHA_ENABLE = 'quickrfq/google_options/captchastatus';
        const CONFIG_CAPTCHA_PRIVATE_KEY = 'quickrfq/google_options/googleprivatekey';
        
        const XML_PATH_UPLOAD_ALLOWED = 'quickrfq/upload/allow';
        
        
        public function execute()
        {
                
                
                $post = $this->getRequest()->getPostValue();
                
                               
                if(!$post){
                        
                        $this->__redirect('*/*/');
                        return;
                }
                
                
                $this->inlineTranslation->suspend();
                
                
                        
                        $postObject = new \Magento\Framework\DataObject();
                        $postObject->setData($post);
                        
                        $error = false;
                        $captcha_enable = false;
                        $captcha_enable = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_ENABLE);
                        
                        if($captcha_enable){
                                if (!\Zend_Validate::is(trim($post["recaptcha_response_field"]) , 'NotEmpty')) { 
                                        $error = true;
                                }
                        }
                        if (!\Zend_Validate::is(trim($post['company']), 'NotEmpty')) {
                                $error = true;
                        }
                        if (!\Zend_Validate::is(trim($post['contact_name']), 'NotEmpty')) {
                                $error = true;
                        }
                        if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                                $error = true;
                        }
                        if (!\Zend_Validate::is(trim($post['overview']), 'NotEmpty')) {
                                $error = true;
                        }
                        if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                                $error = true;
                        }
                        if ($error) {
                                throw new \Exception();
                        }
                        
                        
                        
                        /*Captcha Process*/
                        
                        if($captcha_enable){
                                
                                $recaptcha_path =  BP .'/app/code/FME/Quickrfq/Api/recaptchalib.php';
                                $privatekey = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PRIVATE_KEY);
                                require_once($recaptcha_path);
                                
                                $resp = recaptcha_check_answer($privatekey, $this->remoteAddress->getRemoteAddress(), $post["recaptcha_challenge_field"], $post["recaptcha_response_field"]);
                                if (!$resp->is_valid) {
                                        
                                        throw new \Exception();
                                }
                        }
                        
                        /*Captcha Process*/
                        
                        
                        /*Email Sending Start*/
                        
                        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                        $transport = $this->_transportBuilder
                                        ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                                        ->setTemplateOptions(
                                            [
                                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                                'store' => $this->storeManager->getStore()->getId(),
                                            ]
                                        )
                                        ->setTemplateVars(['data' => $postObject])
                                        ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                                        ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                                        ->setReplyTo($post['email'])
                                        ->getTransport();
                        
                        //createAttachment();
                        
                        $transport->sendMessage();
                        
                        //$message = $this->message;
                        
                        /*Email Sending End*/
                        
                        
                        
                        /* File Uploading Start */
                        
                        $post['prd'] = $this->_processFileUpload();
                        
                        /* File Uploading Ends */
                        
                        
                        /*Save Data Start*/
                        
                        $post['create_date'] = time();
                        $post['update_date'] = time();
                        $model = $this->_objectManager->create('FME\Quickrfq\Model\Quickrfq');
                        $model->setData($post);
                        
                try{        
                        
                        
                        $model->save();                        
                        
                        /*Save Data End*/
                        
                        
                        
                        
                        $this->inlineTranslation->resume();
                        $this->messageManager->addSuccess(
                                __('Thanks for contacting us with your quote request. We\'ll respond to you very soon.')
                        );
                        
                        $this->_redirect('quickrfq/index');
                        return;
                        
                        
                } catch (\Exception $e) {
                        
                        //echo  $e->getMessage().'Error : We can\'t process your request right now'; exit;
                        
                        $this->inlineTranslation->resume();
                        $this->messageManager->addError(
                            __($e->getMessage().' We can\'t process your request right now. Sorry, that\'s all we know.')
                        );
                        $this->_redirect('quickrfq/index');
                        return;
                }
                
                                
        }
        
        
        
        private function _processFileUpload(){
            
            
                if ($_FILES['prd']['error'] > 0) {
                    return false;
                }
                
                try{
                        
                        $Uploader = $this->_objectManager->create(
                                                                        'Magento\MediaStorage\Model\File\Uploader',
                                                                        ['fileId' => 'prd']
                                                                );                        
                        $result = $Uploader->validateFile();
                                
                                
                        if(isset($result) && !empty($result['name'])){
                                        
                                $file_ext_allowed = $this->scopeConfig->getValue(self::XML_PATH_UPLOAD_ALLOWED); 
                                        
                                $Uploader->setAllowedExtensions(explode(',', $file_ext_allowed));
                                $Uploader->setAllowCreateFolders(true);
                                $Uploader->setAllowRenameFiles(true);
                                        
                                $media_dir_obj = $this->_objectManager->get('Magento\Framework\Filesystem')
                                                                                ->getDirectoryRead(DirectoryList::MEDIA);                                                                        
                                $media_dir = $media_dir_obj->getAbsolutePath();
                                        
                                $quickrfq_dir = $media_dir.'/Quickrfq/';   
                                
                                
                                        $Uploader->save($quickrfq_dir);                                
                                        return 'Quickrfq/'.$Uploader->getUploadedFileName();
                                
                        }
                
                } catch (\Exception $e) {
                                //echo $e->getMessage(); exit; 
                                $this->messageManager->addError(
                                    __($e->getMessage())
                                );                                
                }
        }
        
}