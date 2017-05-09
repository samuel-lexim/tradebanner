<?php
namespace FME\Quickrfq\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;
use Magebright\CustomerApprove\Helper\Data;

class Post extends \FME\Quickrfq\Controller\Index
{

    const CONFIG_CAPTCHA_ENABLE = 'quickrfq/google_options/captchastatus';
    const CONFIG_CAPTCHA_PRIVATE_KEY = 'quickrfq/google_options/googleprivatekey';

    const XML_PATH_UPLOAD_ALLOWED = 'quickrfq/upload/allow';

    private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";

    private static $_version = "php_1.0";

    protected $helper;

    /**
     * Post constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        Data $helper
    ) {
        parent::__construct($context, $transportBuilder, $inlineTranslation, $scopeConfig, $storeManager, $remoteAddress);
        $this->helper = $helper;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $this->__redirect('*/*/');
            return;
        }

        $this->inlineTranslation->suspend();

        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($post);

        $error = false;
        $captcha_enable = false;
        $captcha_enable = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_ENABLE);

        if ($captcha_enable) {
            if (!\Zend_Validate::is(trim($post["g-recaptcha-response"]), 'NotEmpty')) {
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

        if ($captcha_enable) {
            $captcha = $post["g-recaptcha-response"];
            $secret = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PRIVATE_KEY);

            $response = null;
            $path = self::$_siteVerifyUrl;
            $dataC = [
                'secret' => $secret,
                'remoteip' => $_SERVER["REMOTE_ADDR"],
                'v' => self::$_version,
                'response' => $captcha
            ];
            $req = "";
            foreach ($dataC as $key => $value) {
                $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
            }
            // Cut the last '&'
            $req = substr($req, 0, strlen($req) - 1);
            $response = file_get_contents($path . $req);
            $answers = json_decode($response, true);
            if (trim($answers ['success']) == true) {
                $error = false;
            } else {
                // Dispay Captcha Error

                $error = true;
                //throw new \Exception();
            }
        }

        /*Captcha Process*/


        /*Email Sending Start*/
        if ($error == false) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $storeId = $this->storeManager->getStore()->getId();

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                ->setReplyTo($post['email'])
                ->getTransport();

            //createAttachment();
            //$transport->sendMessage();
            //$message = $this->message;

            /*Email Sending End*/


            /* File Uploading Start */
            $post['prd'] = $this->_processFileUpload();
            /* File Uploading Ends */


            // Send email to admin
            $templateId = 'custom_estimate_email_template';

            $postData = [
                'custom_quote_name' => $post['contact_name'],
                'customer_email' => $post['email'],
                'company' => $post['company'],
            ];
            $emailObj = new DataObject($postData);

            $templateData = [
                'store' => $this->helper->getStore($storeId),
                'data' => $emailObj
            ];

            $adminEmail =  $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $this->helper->sendEmailTemplate(
                'Custom Estimate Manager',
                $adminEmail,
                $templateId,
                $this->helper->getSender(null, $storeId),
                $templateData,
                $storeId
            );
            // # Send email


            /*Save Data Start*/

            $post['create_date'] = time();
            $post['update_date'] = time();
            $model = $this->_objectManager->create('FME\Quickrfq\Model\Quickrfq');
            $model->setData($post);

            try {
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
                    __($e->getMessage() . ' We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->_redirect('quickrfq/index');
                return;
            }
        } else {
            $this->messageManager->addError(
                __(' Invalid captcha key.')
            );
            $this->_redirect('quickrfq/index');
            return;
        }
    }


    private function _processFileUpload()
    {
        try {
            $Uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'prd']
            );
        } catch (\Exception $e) {
            return false;
        }


        if ($Uploader->validateFile()['error'] > 0) {
            return false;
        }

        try {
            $result = $Uploader->validateFile();


            if (isset($result) && !empty($result['name'])) {
                $file_ext_allowed = $this->scopeConfig->getValue(self::XML_PATH_UPLOAD_ALLOWED);

                $Uploader->setAllowedExtensions(explode(',', $file_ext_allowed));
                $Uploader->setAllowCreateFolders(true);
                $Uploader->setAllowRenameFiles(true);

                $media_dir_obj = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $media_dir = $media_dir_obj->getAbsolutePath();

                $quickrfq_dir = $media_dir . '/Quickrfq/';


                $Uploader->save($quickrfq_dir);
                return 'Quickrfq/' . $Uploader->getUploadedFileName();
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }
    }
}
