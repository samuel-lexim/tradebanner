<?php

namespace Lexim\Override\Model;

class QuoteModelQuoteAddress extends \Magento\Quote\Model\Quote\Address {

    public function getGroupedAllShippingRates()
    {
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