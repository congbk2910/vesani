/**
 * @component  Goivvy_DecimalPlaces
 * @copyright  Copyright (c) 2017 Goivvy.com. (https://www.goivvy.com)
 * @license    https://www.goivvy.com/license Commercial License
 * @author     Goivvy.com developer team <team@goivvy.com>
 */
define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    var globalPriceFormat = {
        requiredPrecision: 2,
        integerRequired: 1,
        decimalSymbol: ',',
        groupSymbol: ',',
        groupLength: ','
    };

    return {
        formatPrice: formatPrice,
        deepClone: objectDeepClone,
        strPad: stringPad,
        findOptionId: findOptionId
    };

    function formatPrice(amount, format, isShowSign) {
        format = _.extend(globalPriceFormat, format);
        var precision = isNaN(format.requiredPrecision = Math.abs(format.requiredPrecision)) ? 2 : format.requiredPrecision,
            integerRequired = isNaN(format.integerRequired = Math.abs(format.integerRequired)) ? 1 : format.integerRequired,
            decimalSymbol = format.decimalSymbol === undefined ? ',' : format.decimalSymbol,
            groupSymbol = format.groupSymbol === undefined ? '.' : format.groupSymbol,
            groupLength = format.groupLength === undefined ? 3 : format.groupLength,
            pattern = format.pattern || '%s',
            s = '',
            i, pad,
            j, re, r, am;
        if (isShowSign === undefined || isShowSign === true) {
            s = amount < 0 ? '-' : (isShowSign ? '+' : '');
        } else if (isShowSign === false) {
            s = '';
        }
        pattern = pattern.indexOf('{sign}') < 0 ? s + pattern : pattern.replace('{sign}', s);
        i = parseInt(amount = Number(Math.round(Math.abs(+amount || 0) + 'e+' + precision) + ('e-' + precision)) , 10) + '';
        pad = (i.length < integerRequired) ? (integerRequired - i.length) : 0;

        i = stringPad('0', pad) + i;

        j = i.length > groupLength ? i.length % groupLength : 0;
        re = new RegExp('(\\d{' + groupLength + '})(?=\\d)', 'g');
        am = Number(Math.round(Math.abs(amount - i) + 'e+' + precision) + ('e-' + precision));
        r = (j ? i.substr(0, j) + groupSymbol : '') +
            i.substr(j).replace(re, '$1' + groupSymbol) +
            (precision ? decimalSymbol + am.toFixed(precision).replace(/-/, 0).slice(2) : '');

        return pattern.replace('%s', r).replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    }
    function stringPad(string, times) {
        return (new Array(times + 1)).join(string);
    }

    function objectDeepClone(obj) {
        return JSON.parse(JSON.stringify(obj));
    }
    function findOptionId(element) {
        if (!element) {
            return;
        }
        var re, id,
            name = $(element).attr('name');

        if (name.indexOf('[') !== -1) {
            re = /\[([^\]]+)?\]/;
        } else {
            re = /_([^\]]+)?_/; 
        }
        id = re.exec(name) && re.exec(name)[1];

        if (id) {
            return id;
        }
    }
});
