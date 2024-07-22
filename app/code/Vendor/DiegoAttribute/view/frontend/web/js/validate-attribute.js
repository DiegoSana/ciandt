define([
    'jquery',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';

    $.validator.addMethod(
        'diego-attribute-validation', function (value) {
            // some custom validation rule
            return value.length >= 5;
        }, $.mage.__('Please insert a valid value. Minimum length: 5'));
});
