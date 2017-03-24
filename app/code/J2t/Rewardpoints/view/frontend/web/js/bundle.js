/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Magento_Catalog/js/price-utils',
    'Magento_Catalog/js/price-box'
], function ($,_, utils) {
    "use strict";

    var globalOptions = {
        optionConfig: null,
        //productBundleSelector: '.product.bundle.option',
		productBundleSelector: 'input.bundle.option, select.bundle.option, textarea.bundle.option',
        qtyFieldSelector: 'input.qty',
        pointBoxSelector: '.product-info-main .j2t-pts, .block-bundle-summary .j2t-pts',
		pointBoxArea: '.product-info-main .rewardpoints-product-point-text, .block-bundle-summary .rewardpoints-product-point-text',
        equivalenceBoxSelector: '.product-info-main .j2t-point-equivalence, .block-bundle-summary .j2t-point-equivalence',
		optionHandlers: {},
        controlContainer: 'dd', // should be eliminated
		defaultPoints: 0,
		currentPoints: 0
    };

    $.widget('mage.pointsBundle', {
        options: globalOptions,
        _init: function initPointBundle() {
            this.options.currentPoints = this.options.defaultPoints;
			var form = this.element;
			//var bundleOptions = $(this.options.productBundleSelector, form);
        },
        _create: function createPointBundle() {
			var form = this.element,
                options = $(this.options.productBundleSelector, form),
                pointBox = $(this.options.pointBoxSelector, form),
                qty = $(this.options.qtyFieldSelector, form);
			
			options.on('change', this._calculatePoints.bind(this));
			qty.on('change', this._onQtyFieldChanged.bind(this));
			
			//var form = this.element;
			//var bundleOptions = $(this.options.productBundleSelector, form);
			//var pointBox = $(this.options.pointBoxSelector, form);
			//var qtyFields = $(this.options.qtyFieldSelector, form);
			//bundleOptions.on('change', calculatePoints.bind(this));
			//qtyFields.on('change', onQtyFieldChanged.bind(this));
		},
        _setOptions: function setOptions(options) {
			$.extend(true, this.options, options);

			if('disabled' in options) {
				this._setOption('disabled', options.disabled);
			}
			return this;
			
			
            /*$.extend(true, this.options, options);
            this._super(options);
            return this;*/
        },
		_calculatePoints: function calculatePoints(event) {
			this.options.currentPoints = this.options.defaultPoints;

			var form = this.element;
			var bundleOptions = $(this.options.productBundleSelector, form);

			var _parent = this;
			bundleOptions.each(function(index, value) { 
				$(this).data('optionContainer', $(this).closest(_parent.options.controlContainer));
				$(this).data('qtyField', $(this).data('optionContainer').find(_parent.options.qtyFieldSelector));
				defaultGetOptionValue($(this), _parent.options.optionConfig, _parent);
			});
			if (this.options.currentPoints > 0){
				$(this.options.pointBoxArea).show();
				$(this.options.pointBoxSelector).html(this.options.currentPoints);

				$(this.options.equivalenceBoxSelector).show();
				var equivalenceText = this.options.optionConfig.equivalenceText;
				var currentEquivalence = _formatCurrency(this.options.currentPoints / this.options.optionConfig.baseEquivalence, this.options.optionConfig.priceFormat);
				var equivalence_points = equivalenceText.replace('{{points}}', this.options.currentPoints);
				var equivalence_points = equivalence_points.replace('{{equivalence}}', currentEquivalence);
				$(this.options.equivalenceBoxSelector).html(equivalence_points);

			} else {
				$(this.options.pointBoxArea).hide();
				$(this.options.equivalenceBoxSelector).hide();
			}
        },
		_onQtyFieldChanged: function onQtyFieldChanged(event) {
			
			var field = $(event.target),
                optionInstance,
                optionConfig;

            if (field.data('optionId') && field.data('optionValueId')) {
                optionInstance = field.data('option');
                optionConfig = this.options.optionConfig
                    .options[field.data('optionId')]
                    .selections[field.data('optionValueId')];
                optionConfig.qty = field.val();

                optionInstance.trigger('change');
            }
        },
		
    });

    return $.mage.pointsBundle;

	
	function _formatCurrency(price, format) {
		var precision = isNaN(format.requiredPrecision = Math.abs(format.requiredPrecision)) ? 2 : format.requiredPrecision,
			integerRequired = isNaN(format.integerRequired = Math.abs(format.integerRequired)) ? 1 : format.integerRequired,
			decimalSymbol = format.decimalSymbol === undefined ? "," : format.decimalSymbol,
			groupSymbol = format.groupSymbol === undefined ? "." : format.groupSymbol,
			groupLength = format.groupLength === undefined ? 3 : format.groupLength,
			s = '';

		var i = parseInt(price = Math.abs(+price || 0).toFixed(precision), 10) + '',
			pad = (i.length < integerRequired) ? (integerRequired - i.length) : 0;
		while (pad) {
			i = '0' + i;
			pad--;
		}
		var j = i.length > groupLength ? i.length % groupLength : 0,
			re = new RegExp("(\\d{" + groupLength + "})(?=\\d)", "g");

		/**
		 * replace(/-/, 0) is only for fixing Safari bug which appears
		 * when Math.abs(0).toFixed() executed on "0" number.
		 * Result is "0.-0" :(
		 */
		var r = (j ? i.substr(0, j) + groupSymbol : "") + i.substr(j).replace(re, "$1" + groupSymbol) +
				(precision ? decimalSymbol + Math.abs(price - i).toFixed(precision).replace(/-/, 0).slice(2) : ""),
			pattern = format.pattern.indexOf('{sign}') < 0 ? s + format.pattern : format.pattern.replace('{sign}', s);
		return pattern.replace('%s', r).replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	}

    //function onBundleOptionChanged(event) {
	function onBundleOptionChanged(event) {
        /*jshint validthis: true */
        var changes;
        var bundleOption = $(event.target);
        var pointBox = $(this.options.pointBoxSelector, this.element);
        var handler = this.options.optionHandlers[bundleOption.data('role')];

        bundleOption.data('optionContainer', bundleOption.closest(this.options.controlContainer));
        bundleOption.data('qtyField', bundleOption.data('optionContainer').find(this.options.qtyFieldSelector));

        /*if(handler && handler instanceof Function) {
            changes = handler(bundleOption, this.options.optionConfig, this);
        } else {*/
            changes = defaultGetOptionValue(bundleOption, this.options.optionConfig, this);
        //}

        if(changes){
			//alert('changed');
            //pointBox.trigger('updatePoint', changes);
        }
    }

    function defaultGetOptionValue(element, config, parent) {
		//console.log(element);
		var changes = {},
            optionHash,
            tempChanges,
            qtyField,
            optionId = utils.findOptionId(element[0]),
            optionValue = element.val() || null,
            optionName = element.prop('name'),
            optionType = element.prop('type'),
            optionConfig = config.options[optionId].selections,
            optionQty = 0,
            canQtyCustomize = false,
            selectedIds = config.selected,
			currentPoints = 0;

        switch (optionType) {
            case 'radio':
			
            case 'select-one':
				
				if(optionType === 'radio' && !element.is(':checked')) {
                    return null;
                }
				
                qtyField = element.data('qtyField');
                qtyField.data('option', element);
                if (optionValue) {
                    optionQty = optionConfig[optionValue].qty || 0;
					if (optionQty)
						currentPoints += optionConfig[optionValue].points * optionQty;
                } 
                break;
            case 'select-multiple':
                optionValue = _.compact(optionValue);
                _.each(optionConfig, function(row, optionValueCode) {
                    optionHash = 'bundle-option-' + optionName + '##' + optionValueCode;
                    optionQty = row.qty || 0;
					if (optionQty)
						currentPoints += row.points * optionQty;
                });

                break;
            case 'checkbox':
                optionHash = 'bundle-option-' + optionName + '##' + optionValue;
                optionQty = optionConfig[optionValue].qty || 0;
				if (optionQty)
					currentPoints += optionConfig[optionValue].points * optionQty;
                break;
            case 'hidden':
                optionHash = 'bundle-option-' + optionName + '##' + optionValue;
                optionQty = optionConfig[optionValue].qty || 0;
				if (optionQty)
					currentPoints += optionConfig[optionValue].points * optionQty;
                break;
        }
		
		//alert(currentPoints);
		parent.options.currentPoints += currentPoints;

        return changes;
    }

});