/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'J2t_Rewardpoints/js/model/points',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function (ko, Component, rewardpointsManager, quote, totals) {
        "use strict";
        var usedPoints = window.usedPoints;
        var pointsQty = ko.observable(usedPoints);
        return Component.extend({
            defaults: {
                template: 'J2t_Rewardpoints/summary/rewardpoints'
            },
            //totals: quote.getTotals(),
            totals: totals.totals,
            pointsQty: pointsQty,
            getRewardpointsQty: function() {
                if (rewardpointsManager.getPointsQty() != null){
                    return rewardpointsManager.getPointsQty(); 
                }
                return null;
            },
            isButtonRemoveAvailable: function() {
                if (rewardpointsManager.isButtonRemoveAvailable() != null){
                    return rewardpointsManager.isButtonRemoveAvailable(); 
                }
                
                return false;
            },
            
            
            setRewardpointsQty: function(qty) {
                this.pointsQty = qty;
            },
            
            getRewardpointsSegment: function () {
                //console.log(quote.totals());
                var rewardpoints = totals.getSegment('rewardpoints');
                if (rewardpoints !== null && rewardpoints.hasOwnProperty('value')) {
                    return rewardpoints.value;
                }
                return 0;
            },
            
            getPureValue: function() {
                var price = parseFloat(this.getRewardpointsSegment());
                return price;
            },
            
            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            },
            
            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            }
        });
    }
);
