define([
    'underscore',
    'jquery'
], function (_, $) {
    "use strict";

    return function (initiatedObject, selector) {
        if (!_.isObject(initiatedObject) || _.isEmpty(selector)) {
            throw 'RuntimeError: Invalid loader arguments';
        }

        $.async(selector, function () {
            initiatedObject.loader = $(selector).loader();
            initiatedObject.show = function () {
                    this.loader.loader('show', null, this.loader);
            };
            initiatedObject.hide = function () {
                    this.loader.loader('hide');
            };
        });
    }
});
