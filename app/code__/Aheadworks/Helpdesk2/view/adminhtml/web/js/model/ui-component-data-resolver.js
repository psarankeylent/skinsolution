define([
    'uiRegistry'
], function (registry) {
    "use strict";

    return function (link) {
        var parsed, component, field;

        parsed = link.split(':');
        if (parsed[0] && parsed[1]) {
            component = parsed[0];
            field = parsed[1];

            if (registry.has(component)) {
                return registry.get(component).get(field);
            }
        }

        return undefined;
    };
});
