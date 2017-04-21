<?php
/**
 * @author Samuel Kong
 */

namespace Lexim\Override\Controller\Fedex;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Request\Http;

use \Magento\Quote\Model\Quote\Address\RateRequestFactory;
use \Magento\Fedex\Model\Carrier;


class Rate extends Action
{
    protected $_request;
    protected $_fedex;
    protected $_rateRequestFactory;


    /**
     * Rate constructor.
     * @param Context $context
     * @param Http $request
     * @param Carrier $fedex
     * @param RateRequestFactory $rateRequestFactory
     */
    public function __construct(
        Context $context,
        Http $request,
        Carrier $fedex,
        RateRequestFactory $rateRequestFactory
    )
    {
        $this->_request = $request;
        $this->_fedex = $fedex;
        $this->_rateRequestFactory = $rateRequestFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $zipCode = $this->getRequest()->getParam('z');
        $weight = $this->getRequest()->getParam('w');
        $price = $this->getRequest()->getParam('p');

        $request = $this->_rateRequestFactory->create();
        $price = floatval($price);
        $price = ($price <= 0) ? -1 : $price;
        $request->setPackagePhysicalValue($price);
        $request->setDestCountryId('US');
        $request->setDestPostcode($zipCode);
        $request->setWeight($weight);

        $result = $this->_fedex->calcFedexRate($request);

        echo json_encode($result, true);
    }


}