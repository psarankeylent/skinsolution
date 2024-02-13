define([
    'uiRegistry'
], function (registry) {
    "use strict";

    return function (componentName) {
        registry.async(componentName)(
            function (component) {
                if (component.source) {
                    component.source.reload({refresh: true});
                }
            }.bind(this)
        );
    }
});
