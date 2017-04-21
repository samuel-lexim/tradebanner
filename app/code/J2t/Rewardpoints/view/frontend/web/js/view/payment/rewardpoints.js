/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
        [
            'jquery',
            'ko',
            'uiComponent',
            'Magento_Checkout/js/model/quote',
            'J2t_Rewardpoints/js/action/set-rewardpoints',
            'J2t_Rewardpoints/js/action/cancel-rewardpoints',
            'J2t_Rewardpoints/js/model/points',
            'jquery/ui',
            'J2t_Rewardpoints/js/jquery-ui-slider-pips'
        ],
        function ($, ko, Component, quote, setRewardpointsAction, cancelRewardpointsAction, rewardpointsManager) {
            'use strict';
            var totals = quote.getTotals();
            var rewardpointsQuantity = ko.observable(null);
            rewardpointsQuantity(null);
            if (rewardpointsManager.getPointsQty() != null){
                rewardpointsQuantity(rewardpointsManager.getPointsQty());
            }
            /*var usedPoints = window.usedPoints;
            var rewardpointsQuantity = ko.observable(usedPoints);*/
            
            
            /*if (totals()) {
                var points = totals()['rewardpoints_quantity'];
                if (points == 0) {
                    points = null;
                }
                rewardpointsQuantity(points);
            }*/
            var isList = ko.observable(false);
            var isSlider = ko.observable(false);
            var stepsList = ko.observableArray([]);
            var isApplied = ko.observable(rewardpointsQuantity() != null);
            var isLoading = ko.observable(false);
            var shouldShowField = ko.observable(true);
            var showSelectField = ko.observable(window.showSelectField);
            var isRewardCustomerLogged = ko.observable(window.isRewardCustomerLogged);
            var isActive = ko.observable(window.isRewardpointsActive);

            return Component.extend({
                defaults: {
                    template: 'J2t_Rewardpoints/payment/rewardpoints'
                },
                rewardpointsQuantity: rewardpointsQuantity,
                /**
                 * Applied flag
                 */
                isApplied: isApplied,
                isLoading: isLoading,
                isList: isList,
                isSlider: isSlider,
                stepsList: stepsList,
                shouldShowField: shouldShowField,
                showSelectField: showSelectField,
                isRewardCustomerLogged: isRewardCustomerLogged,
                isActive: isActive,
                initChildren: function () {
                    //this.loadSteps();
                },
                /**
                 * Load steps procedure
                 */
                loadSteps: function () {
                    if (window.isSlider) {
                        showSelectField(false);
                        shouldShowField(false);
                        var defaultLocation = jQuery.inArray(window.defaultSlider, window.rewardStepConfig);
                        $("#slider").slider({
                            value: defaultLocation,
                            min: 0,
                            max: window.rewardStepConfig.length - 1,
                            /*create: function( event, ui ) {
                             $( "#rewardpoints_value" ).val(values[ui.value]);
                             },*/
                            slide: function (event, ui) {
                                $("#rewardpoints-quantity").val(window.rewardStepConfig[ui.value]);
                                $(".applyPointsBtn").removeAttr("disabled");
                                rewardpointsQuantity(window.rewardStepConfig[ui.value]);
                            }
                        }).slider("pips", {
                            labels: window.rewardStepConfig
                        }).slider("float", {
                            labels: window.rewardStepConfig
                        });
                    } else if (window.showSelectField) {
                        shouldShowField(false);
                    }
                },
                /**
                 * Points application procedure
                 */
                apply: function () {
                    if (this.validate()) {
                        isLoading(true);
                        setRewardpointsAction(rewardpointsQuantity(), isApplied, isLoading, rewardpointsQuantity);
                        //this.updateQtyField();
                        //rewardpointsQuantity(rewardpointsManager.getPointsQty());
                    }
                },
                /**
                 * Cancel using points
                 */
                cancel: function () {
                    if (this.validate()) {
                        isLoading(true);
                        rewardpointsQuantity('');
                        cancelRewardpointsAction(isApplied, isLoading);
                    }
                },
                updateQtyField: function () {
                    if (rewardpointsManager.getPointsQty() != null){
                        return rewardpointsManager.getPointsQty(); 
                    }

                    return null;
                },
                /**
                 * Points form validation
                 *
                 * @returns {boolean}
                 */
                validate: function () {
                    var form = '#rewardpoints-form';
                    return $(form).validation() && $(form).validation('isValid');
                }
            });
        }
);
