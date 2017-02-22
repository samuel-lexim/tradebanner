<?php
namespace FME\Quickrfq\Block;

use Magento\Framework\View\Element\Template;

class Postform extends Template
{
    const CONFIG_CAPTCHA_PUBLIC_KEY = 'quickrfq/google_options/googlepublickey';
    const CONFIG_CAPTCHA_THEME = 'quickrfq/google_options/theme';
    const CONFIG_CAPTCHA_ENABLE = 'quickrfq/google_options/captchastatus';

    const CONFIG_FILE_EXT_UPLOAD = 'quickrfq/upload/allow';

    protected $scopeConfig;


    public function __construct(\Magento\Framework\View\Element\Template\Context $context)
    {

        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }


    public function getFormAction()
    {
        return $this->getUrl('quickrfq/index/post', ['_secure' => true]);
    }


    public function getCaptchaTheme()
    {

        $theme = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_THEME);
        return $theme;
    }

    public function isCaptchaEnable()
    {

        $enable = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_ENABLE);
        return $enable;
    }

    public function getAllowedFileExtensions()
    {

        $ext = $this->scopeConfig->getValue(self::CONFIG_FILE_EXT_UPLOAD);
        return $ext;
    }

    public function getPublicKey()
    {

        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PUBLIC_KEY);
    }
}
