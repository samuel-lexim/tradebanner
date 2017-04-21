define([
    "jquery",
    "jquery/ui"
], function($) {
    "use strict";
    console.log('j2t.rewardpointsColorswatch is loaded!!');
    //console.log(logger);
 
        //creating jquery widget
        $.widget('j2t.rewardpointsColorswatch', {
            options: {
                selector: 'swatch-option', //swatch-opt-729 on product list (729 = product id)  
                superSelector: '.swatch-attribute',
                state: {},
                optionTemplate: '{{label}}' +
                        '{{#if finalPrice.value}}' +
                        ' {{finalPrice.formatted}}' +
                        '{{/if}}',
                pointAreaSelector: '#product_addtocart_form .j2t-pts',
                pointEquivalenceSelector: '#product_addtocart_form .j2t-point-equivalence'
            },
            _updatePoints: function(element) {
                var attributeId = $(element).closest( this.options.superSelector ).attr('attribute-id');
                var selectValue = parseInt($(element).closest( this.options.superSelector ).attr('option-selected'), 10);
                var points = this.options.spConfig.points,
                currentPoint = this.options.spConfig.basePoints,
                currentEquivalence = this.options.spConfig.baseEquivalence,
                equivalence = this.options.spConfig.equivalence,
                pointsElement = $(this.options.pointAreaSelector),
                pointsEquivalenceElement = $(this.options.pointEquivalenceSelector),
                equivalenceText = this.options.spConfig.equivalenceText;

                var pointsArray = null;
                var equivalenceArray = null;
                var canProcess = true;
                $.each($(this.options.superSelector), function (k, v) {
                    //var selectValue = parseInt(v.value, 10),
                    //        attributeId = v.id.replace(/[a-z]*/, '');
                    //var attr = $(this).attr('option-selected');
                    var attr = $(this).attr('option-selected');
                    if (typeof attr === typeof undefined || attr === false) {
                      canProcess = false;
                    }
                });
                
                if (canProcess){
                    if (selectValue > 0 && attributeId) {
                        if (!pointsArray) {
                            pointsArray = points[attributeId][selectValue];
                            equivalenceArray = equivalence[attributeId][selectValue];
                        } else {
                            var intersectedArray = {};
                            var intersectedEquivalenceArray = {};
                            $.each(pointsArray, function (productId) {
                                if (points[attributeId][selectValue][productId]) {
                                    intersectedArray[productId] = points[attributeId][selectValue][productId];
                                    intersectedEquivalenceArray[productId] = equivalence[attributeId][selectValue][productId];
                                }
                            });
                            pointsArray = intersectedArray;
                            equivalenceArray = intersectedEquivalenceArray;
                        }
                    }

                    var currentEquivalence = "";

                    $.each(pointsArray, function (k, v) {
                        currentPoint = v;
                        currentEquivalence = equivalenceArray[k];
                    });
                    pointsElement.html(currentPoint);
                    var equivalence_points = equivalenceText.replace('{{points}}', currentPoint);
                    var equivalence_points = equivalence_points.replace('{{equivalence}}', currentEquivalence);
                    pointsEquivalenceElement.html(equivalence_points);
                }
            },
            _create: function() {
                var parent = this;
                this.element.on('click', function(e){
                    if ($(e.target).hasClass(parent.options.selector)){
                        parent._updatePoints(e.target);
                    }
                });
            }
 
        });
 
    return $.j2t.rewardpointsColorswatch;
});