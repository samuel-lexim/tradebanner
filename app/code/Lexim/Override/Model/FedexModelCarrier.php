<?php
/**
 * @author Samuel Kong
 */

namespace Lexim\Override\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;

class FedexModelCarrier extends \Magento\Fedex\Model\Carrier
{


    /**
     * @param RateRequest $request
     * @return string
     */
    public function calcFedexRate(RateRequest $request)
    {
        $r = new \Magento\Framework\DataObject();

        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        }

        if ($request->getFedexAccount()) {
            $account = $request->getFedexAccount();
        } else {
            $account = $this->getConfigData('account');
        }
        $r->setAccount($account);

        if ($request->getFedexDropoff()) {
            $dropoff = $request->getFedexDropoff();
        } else {
            $dropoff = $this->getConfigData('dropoff');
        }
        $r->setDropoffType($dropoff);

        if ($request->getFedexPackaging()) {
            $packaging = $request->getFedexPackaging();
        } else {
            $packaging = $this->getConfigData('packaging');
        }
        $r->setPackaging($packaging);

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = $this->_scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStoreId()
            );
        }
        $r->setOrigCountry($this->_countryFactory->create()->load($origCountry)->getData('iso2_code'));

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );
        }

        $desCountry = 'US';
        $r->setDestCountry($this->_countryFactory->create()->load($desCountry)->getData('iso2_code'));
        $r->setDestPostal($request->getDestPostcode());

        $weight = (!is_null($request->getWeight()) && floatval($request->getWeight()) > 0) ? $request->getWeight() : 3;
        $r->setWeight($weight);
        $r->setValue($request->getPackagePhysicalValue());

        $r->setMeterNumber($this->getConfigData('meter_number'));
        $r->setKey($this->getConfigData('key'));
        $r->setPassword($this->getConfigData('password'));

        $r->setBaseSubtotalInclTax($request->getPackagePhysicalValue());
        //$this->setRawRequest($r);


        $ratesRequest = [
            'WebAuthenticationDetail' => [
                'UserCredential' => ['Key' => $r->getKey(), 'Password' => $r->getPassword()],
            ],
            'ClientDetail' => ['AccountNumber' => $r->getAccount(), 'MeterNumber' => $r->getMeterNumber()],
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => [
                'DropoffType' => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => ['Amount' => $r->getValue(), 'Currency' => $this->getCurrencyCode()],
                'Shipper' => [
                    'Address' => ['PostalCode' => $r->getOrigPostal(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'Recipient' => [
                    'Address' => [
                        'PostalCode' => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => (bool)$this->getConfigData('residence_delivery'),
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => ['AccountNumber' => $r->getAccount(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'CustomsClearanceDetail' => [
                    'CustomsValue' => ['Amount' => $r->getValue(), 'Currency' => $this->getCurrencyCode()],
                ],
                'RateRequestTypes' => 'LIST',
                'PackageCount' => '1',
                'PackageDetail' => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => [
                    '0' => [
                        'Weight' => [
                            'Value' => (double)$r->getWeight(),
                            'Units' => $this->getConfigData('unit_of_measure'),
                        ],
                        'GroupPackageCount' => 1,
                    ],
                ],
            ],
        ];
        $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = [
            'Amount' => $r->getValue(),
            'Currency' => $this->getCurrencyCode(),
        ];


        $client = $this->_createRateSoapClient();
        $response = $client->getRates($ratesRequest);


        //$costArr = [];
        $priceArr = [];
        $errorTitle = 'For some reason we can\'t retrieve tracking info right now.';
        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                if (is_array($response->Notifications)) {
                    $notification = array_pop($response->Notifications);
                    $errorTitle = (string)$notification->Message;
                } else {
                    $errorTitle = (string)$response->Notifications->Message;
                }
            } elseif (isset($response->RateReplyDetails)) {
                $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

                if (is_array($response->RateReplyDetails)) {
                    foreach ($response->RateReplyDetails as $rate) {
                        $serviceName = (string)$rate->ServiceType;
                        if (in_array($serviceName, $allowedMethods)) {
                            $amount = $this->_getRateAmountOriginBased($rate);
                            //$costArr[$serviceName] = $amount;
                            $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                        }
                    }
                    asort($priceArr);
                } else {
                    $rate = $response->RateReplyDetails;
                    $serviceName = (string)$rate->ServiceType;
                    if (in_array($serviceName, $allowedMethods)) {
                        $amount = $this->_getRateAmountOriginBased($rate);
                        //$costArr[$serviceName] = $amount;
                        $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                    }
                }
            }
        }


        if (empty($priceArr)) {
            return $errorTitle;
        } else {
            $fedex = [];
            foreach ($priceArr as $method => $price) {
                $title = $this->getFedexMethodByCode($method);
                if($title=='FedEx Ground'){
                    $price = $price*1.3 + 4.00;
                } else if ($title=='FedEx Ground1'){
                    $price = $price*1.3;
                } else if ($title=='FedEx Express Saver (3 days)'){
                    $price = $price*1.28+8.00;
                } else if ($title=='FedEx 2 Day'){
                    $price = $price*1.3+5.00;
                } else if ($title=='FedEx 2 Day AM'){
                    $price = $price*1.3+8.00;
                } else if ($title=='FedEx Standard Overnight'){
                    $price = $price*1.35+10.00;
                } else if ($title=='FedEx Priority Overnight'){
                    $price = $price*1.35+10.00;
                } else if ($title=='FedEx First Overnight'){
                    $price = $price*1.4+10.00;
                }
                $price= round($price, 2);
                if ($title && $title != '') {
                    $fedex[$method] = [
                        'name' => $this->getFedexMethodByCode($method),
                        'price' => '$' . $price
                    ];
                }
            }
            return $fedex;
        }
    }

    /**
     * @param string $code
     * @return string
     */
    public function getFedexMethodByCode($code = '')
    {
        $method = [
            'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'Europe First Priority',
            'FEDEX_1_DAY_FREIGHT' => '1 Day Freight',
            'FEDEX_2_DAY_FREIGHT' => '2 Day Freight',
            'FEDEX_2_DAY' => 'FedEx 2 Day',
            'FEDEX_2_DAY_AM' => 'FedEx 2 Day AM',
            'FEDEX_3_DAY_FREIGHT' => '3 Day Freight',
            'FEDEX_EXPRESS_SAVER' => 'FedEx Express Saver (3 days)',
            'FEDEX_GROUND' => 'FedEx Ground',
            'FIRST_OVERNIGHT' => 'FedEx First Overnight',
            'GROUND_HOME_DELIVERY' => 'FedEx Ground1',
            'INTERNATIONAL_ECONOMY' => 'International Economy',
            'INTERNATIONAL_ECONOMY_FREIGHT' => 'Intl Economy Freight',
            'INTERNATIONAL_FIRST' => 'International First',
            'INTERNATIONAL_GROUND' => 'International Ground',
            'INTERNATIONAL_PRIORITY' => 'International Priority',
            'INTERNATIONAL_PRIORITY_FREIGHT' => 'Intl Priority Freight',
            'PRIORITY_OVERNIGHT' => 'FedEx Priority Overnight',
            'SMART_POST' => 'Smart Post',
            'STANDARD_OVERNIGHT' => 'FedEx Standard Overnight',
            'FEDEX_FREIGHT' => 'Freight',
            'FEDEX_NATIONAL_FREIGHT' => 'National Freight'

            // 'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'Europe First Priority',
            // 'FEDEX_1_DAY_FREIGHT' => '1 Day Freight',
            // 'FEDEX_2_DAY_FREIGHT' => '2 Day Freight',
            // 'FEDEX_2_DAY' => '2 Day',
            // 'FEDEX_2_DAY_AM' => '2 Day AM',
            // 'FEDEX_3_DAY_FREIGHT' => '3 Day Freight',
            // 'FEDEX_EXPRESS_SAVER' => 'Express Saver',
            // 'FEDEX_GROUND' => 'Ground',
            // 'FIRST_OVERNIGHT' => 'First Overnight',
            // 'GROUND_HOME_DELIVERY' => 'Home Delivery',
            // 'INTERNATIONAL_ECONOMY' => 'International Economy',
            // 'INTERNATIONAL_ECONOMY_FREIGHT' => 'Intl Economy Freight',
            // 'INTERNATIONAL_FIRST' => 'International First',
            // 'INTERNATIONAL_GROUND' => 'International Ground',
            // 'INTERNATIONAL_PRIORITY' => 'International Priority',
            // 'INTERNATIONAL_PRIORITY_FREIGHT' => 'Intl Priority Freight',
            // 'PRIORITY_OVERNIGHT' => 'Priority Overnight',
            // 'SMART_POST' => 'Smart Post',
            // 'STANDARD_OVERNIGHT' => 'Standard Overnight',
            // 'FEDEX_FREIGHT' => 'Freight',
            // 'FEDEX_NATIONAL_FREIGHT' => 'National Freight'
        ];

        return isset($method[$code]) ? $method[$code] : '';
    }

}