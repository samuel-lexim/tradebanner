/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    ['ko'],
    function (ko) {
        'use strict';
        var usedPoints = window.usedPoints;
        var buttonRemove = window.buttonRemove;
        var pointsQty = ko.observable(usedPoints);
        var hasButtonRemove = ko.observable(buttonRemove);
        return {
            pointsQty: pointsQty,
            hasButtonRemove: hasButtonRemove,
            isButtonRemoveAvailable: function() {
                return hasButtonRemove();
            },
            getPointsQty: function() {
                return pointsQty();
            },
            setPointsQty: function(qty) {
                pointsQty(qty);
            }
        };
    }
);
