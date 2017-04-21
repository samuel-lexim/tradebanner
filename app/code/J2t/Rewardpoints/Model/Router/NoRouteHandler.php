<?php
/**
 * Default no route handler
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace J2t\Rewardpoints\Model\Router;

use \Magento\Framework\App\Router\NoRouteHandlerInterface;

class NoRouteHandler extends \Magento\Framework\App\Router\NoRouteHandler //\Magento\Framework\App\Router\NoRouteHandlerInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    /*public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $config)
    {
        $this->_config = $config;
    }*/

    /**
     * Check and process no route request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process(\Magento\Framework\App\RequestInterface $request)
    {
        /*
         * <rewrite>
            <short_referral_url>
                <from><![CDATA[/referral-program\/(.*)/]]></from>
                <to><![CDATA[rewardpoints/index/goReferral/decript/$1/]]></to>
                <complete>1</complete>
             </short_referral_url>
         </rewrite>
         */
        
        if (strpos($request->getPathInfo(), "referral-program") !== false){
            $noRoute = [];
            $moduleName = 'rewardpoints';
            $actionPath = 'referral';
            $actionName = str_replace("referral-program", "goReferral/referral-program", $request->getPathInfo());

            $request->setModuleName($moduleName)->setControllerName($actionPath)->setActionName($actionName);
            return true;
        } else {
            return parent::process($request);
        }
        return true;
    }
}
