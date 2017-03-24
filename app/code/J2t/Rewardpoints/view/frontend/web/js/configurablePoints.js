/**
 * Copyright © 2016 J2T Design. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    /*"jquery",
    "underscore",
    "handlebars",
    "jquery/ui",
    "jquery/jquery.parsequery",
    "Magento_Catalog/js/price-box"*/
    
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, _, mageTemplate) {
    'use strict';
    
    $.widget('mage.configurablePoints', {
        options: {
            superSelector: '.super-attribute-select',
            priceHolderSelector: '.price-box',
            state: {},
            optionTemplate: '{{label}}' +
                    '{{#if finalPrice.value}}' +
                    ' {{finalPrice.formatted}}' +
                    '{{/if}}',
            pointAreaSelector: '#product_addtocart_form .j2t-pts',
            pointEquivalenceSelector: '#product_addtocart_form .j2t-point-equivalence'
            //#product_addtocart_form .j2t-point-equivalence
        },
        _create: function () {
            // Initial setting of various option values
            this._initializeOptions();

            // Change events to check select reloads
            this._setupChangeEvents();

            // Setup/configure values to inputs
            this._configureForValues();
        },
        /**
         * Initialize tax configuration, initial settings, and options values.
         * @private
         */
        _initializeOptions: function () {
            //var priceBoxOptions = $(this.options.priceHolderSelector).priceBox('option').priceConfig || null;
            
            var priceBoxOptions = $(this.options.priceHolderSelector).priceBox('option');
            if (priceBoxOptions.priceConfig && priceBoxOptions.priceConfig.optionTemplate) {
                //this.options.optionTemplate = priceBoxOptions.priceConfig.optionTemplate;
                this.options.optionTemplate = priceBoxOptions.optionTemplate;
            }
            //this.options.optionTemplate = Handlebars.compile(this.options.optionTemplate);
            this.options.optionTemplate = mageTemplate(this.options.optionTemplate);

            this.options.settings = (this.options.spConfig.containerId) ?
                    $(this.options.spConfig.containerId).find(this.options.superSelector) :
                    $(this.options.superSelector);
        },
        /**
         * Set up .on('change') events for each option element to configure the option.
         * @private
         */
        _setupChangeEvents: function () {
            $.each(this.options.settings, $.proxy(function (index, element) {
                $(element).on('change', this, this._configure);
            }, this));
        },
        /**
         * Iterate through the option settings and set each option's element configuration,
         * attribute identifier. Set the state based on the attribute identifier.
         * @private
         */
        _fillState: function () {
            $.each(this.options.settings, $.proxy(function (index, element) {
                var attributeId = element.id.replace(/[a-z]*/, '');
                if (attributeId && this.options.spConfig.attributes[attributeId]) {
                    element.config = this.options.spConfig.attributes[attributeId];
                    element.attributeId = attributeId;
                    this.options.state[attributeId] = false;
                }
            }, this));
        },
        /**
         * Setup for all configurable option settings. Set the value of the option and configure
         * the option, which sets its state, and initializes the option's choices, etc.
         * @private
         */
        _configureForValues: function () {
            if (this.options.values) {
                this.options.settings.each($.proxy(function (index, element) {
                    var attributeId = element.attributeId;
                    element.value = (typeof (this.options.values[attributeId]) === 'undefined') ?
                            '' :
                            this.options.values[attributeId];
                    this._configureElement(element);
                }, this));
            }
        },
        /**
         * Event handler for configuring an option.
         * @private
         * @param event Event triggered to configure an option.
         */
        _configure: function (event) {
            event.data._configureElement(this);
        },
        /**
         * Configure an option, initializing it's state and enabling related options, which
         * populates the related option's selection and resets child option selections.
         * @private
         * @param element The element associated with a configurable option.
         */
        _configureElement: function (element) {
            //this._reloadOptionLabels(element);
            if (element.value) {
                this.options.state[element.config.id] = element.value;
                if (element.nextSetting) {
                    element.nextSetting.disabled = false;
                }
            } else {
                //this._resetChildren(element);
            }
            this._changeProductPoints(element);
        },
        _changeProductPoints: function () {

            var points = this.options.spConfig.points,
                    currentPoint = this.options.spConfig.basePoints,
                    currentEquivalence = this.options.spConfig.baseEquivalence,
                    equivalence = this.options.spConfig.equivalence,
                    pointsElement = $(this.options.pointAreaSelector),
                    pointsEquivalenceElement = $(this.options.pointEquivalenceSelector),
                    equivalenceText = this.options.spConfig.equivalenceText;

            var pointsArray = null;
            var equivalenceArray = null;
            $.each(this.options.settings, function (k, v) {
                var selectValue = parseInt(v.value, 10),
                        attributeId = v.id.replace(/[a-z]*/, '');
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
            });

            var currentEquivalence = "";
            $.each(pointsArray, function (k, v) {
                currentPoint = v;
                currentEquivalence = equivalenceArray[k];
            });
            pointsElement.html(currentPoint);
            var equivalence_points = equivalenceText.replace('{{points}}', currentPoint);
            var equivalence_points = equivalence_points.replace('{{equivalence}}', currentEquivalence);
            pointsEquivalenceElement.html(equivalence_points);
        },
    });

    return $.mage.configurablePoints;
});
