define([
    'underscore',
    'jquery',
    'mageUtils'
], function (_, $, utils) {
    "use strict";

    function addEmptyArrayFieldsAfterSerialize(beforeSerializeObject, afterSerializeObject) {
        _.each(beforeSerializeObject, function (value, name) {
            if (value instanceof Array && value.length === 0 && !_.has(afterSerializeObject, name)) {
                afterSerializeObject[name] = null;
            }
        });

        return afterSerializeObject;
    }

    return function (url, params) {
        var data, setup;

        data = utils.serialize(params);
        data = addEmptyArrayFieldsAfterSerialize(params, data)
        setup = {
            url: url,
            type: "POST",
            dataType: 'json',
            data: data
        };

        return $.ajax(setup);
    }
});
