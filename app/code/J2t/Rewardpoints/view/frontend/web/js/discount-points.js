/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    
    $.widget('mage.discountPoint', {
        options: {
        },
        _create: function () {
            this.pointQty = $(this.options.rewardPointSelector);
            this.removePoints = $(this.options.removePointSelector);

            $(this.options.applyButton).on('click', $.proxy(function () {
                this.pointQty.attr('data-validate', '{required:true}');
                this.removePoints.attr('value', '0');
                $(this.element).validation().submit();
            }, this));

            $(this.options.cancelButton).on('click', $.proxy(function () {
                this.pointQty.removeAttr('data-validate');
                this.removePoints.attr('value', '1');
                this.element.submit();
            }, this));
        }
    });

    return $.mage.discountPoint;
});