/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'mageUtils'
    ],
    function(customer, urlBuilder, utils) {
        "use strict";
        return {
            getApplyPointsUrl: function(rewardpointsQuantity, quoteId) {
                var params = (this.getCheckoutMethod() == 'guest') ? {quoteId: quoteId} : {};
                var urls = {
                    'guest': '/guest-carts/' + quoteId + '/rewardpoints/' + rewardpointsQuantity,
                    'customer': '/carts/mine/rewardpoints/' + rewardpointsQuantity
                };
                return this.getUrl(urls, params);
            },

            getCancelPointsUrl: function(quoteId) {
                var params = (this.getCheckoutMethod() == 'guest') ? {quoteId: quoteId} : {};
                var urls = {
                    'guest': '/guest-carts/' + quoteId + '/rewardpoints/',
                    'customer': '/carts/mine/rewardpoints/'
                };
                return this.getUrl(urls, params);
            },
			/** Get url for service */
            getUrl: function(urls, urlParams) {
                var url;

                if (utils.isEmpty(urls)) {
                    return 'Provided service call does not exist.';
                }

                if (!utils.isEmpty(urls['default'])) {
                    url = urls['default'];
                } else {
                    url = urls[this.getCheckoutMethod()];
                }
                return urlBuilder.createUrl(url, urlParams);
            },

            getCheckoutMethod: function() {
                return customer.isLoggedIn() ? 'customer' : 'guest';
            }
        };
    }
);
