/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer store credit(balance) application
 */
/*global define,alert*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'J2t_Rewardpoints/js/model/resource-url-manager',
        'J2t_Rewardpoints/js/model/points',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Ui/js/model/messageList',
        'mage/storage',
        'Magento_Checkout/js/action/get-totals',
        'mage/translate'
    ],
    function (ko, $, quote, urlManager, rewardpointsManager, paymentService, errorProcessor, messageList, storage, getTotalsAction, $t) {
        'use strict';
        return function (rewardpointsQuantity, isApplied, isLoading, rewardpointsQuantityFn) {
            var quoteId = quote.getQuoteId();
            var url = urlManager.getApplyPointsUrl(rewardpointsQuantity, quoteId);
            var message = $t('You successfully used your points');
            return storage.put(
                url,
                {},
                false
            ).done(
                function (response) {
                    //console.log(response);
                    if (response) {
                        var deferred = $.Deferred();
                        isLoading(false);
                        isApplied(true);
                        getTotalsAction([], deferred);
                        $.when(deferred).done(function() {
                            paymentService.setPaymentMethods(
                                paymentService.getAvailablePaymentMethods()
                            );
                            window.usedPoints = response;
                            rewardpointsManager.setPointsQty(response);
                            rewardpointsQuantityFn(response);
                            
                        });
                        messageList.addSuccessMessage({'message': message});
                    }
                }
            ).fail(
                function (response) {
                    isLoading(false);
                    errorProcessor.process(response);
                }
            );
        };
    }
);
