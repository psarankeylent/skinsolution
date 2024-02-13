define([
    'underscore',
    './ui-component-data-resolver'
], function (_, resolveComponentData) {
    "use strict";

    return function (payload) {
        var value,
            data = {};

        _.each(payload, function (payloadValue, payloadKey) {
            value = resolveComponentData(payloadValue);
            if (value !== undefined) {
                data[payloadKey] = value;
            } else {
                data[payloadKey] = payloadValue;
            }
        });

        return data;
    };
});
