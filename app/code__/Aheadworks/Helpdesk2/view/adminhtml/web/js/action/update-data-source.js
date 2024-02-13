define([
    'underscore',
], function (_) {
    "use strict";

    return function (source, data) {
        if (_.isObject(source)) {
            _.each(data, function(value, key) {
                source.set('data.' + key, value);
            });
        }
    }
});
