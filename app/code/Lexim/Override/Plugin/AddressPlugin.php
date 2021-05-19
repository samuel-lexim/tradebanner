<?php

namespace Lexim\Override\Plugin;

class AddressPlugin {

    public function getGroupedAllShippingRates()
    {
        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('Custom');
        $rates = [];
        $customer = $this->getQuote()->getCustomer();
        $freeStatus = $customer->getCustomAttribute('free_delivery_kong')->getValue();

        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted() && $this->_carrierFactory->get($rate->getCarrier())) {

                if ($rate->getCarrier() != 'freeshipping' || ($rate->getCarrier() == 'freeshipping' && $freeStatus == '1') ) {

                    if (!isset($rates[$rate->getCarrier()])) {
                        $rates[$rate->getCarrier()] = [];
                    }

                    $rates[$rate->getCarrier()][] = $rate;
                    $rates[$rate->getCarrier()][0]->carrier_sort_order = $this->_carrierFactory->get(
                        $rate->getCarrier()
                    )->getSortOrder();

                }

            }
        }
        uasort($rates, [$this, '_sortRates']);
        return $rates;
    }
}