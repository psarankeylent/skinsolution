define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/components/button',
    'Aheadworks_Helpdesk2/js/model/compose-payload-from-source',
    'Aheadworks_Helpdesk2/js/action/ticket/send-request',
    'Aheadworks_Helpdesk2/js/model/backend-message-manager',
    'Aheadworks_Helpdesk2/js/model/block-loader',
], function (_, registry, Button, composePayload, sendRequest, messageManager, blockLoader) {
    'use strict';

    return Button.extend({
        defaults: {
            blockLoaderSelector: null
        },

        loader: {},

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super();
            if (this.blockLoaderSelector) {
                this.initLoader();
            }

            return this;
        },
        
        /**
         * @inheritdoc
         */
        applyAction: function (action) {
            var self = this,
                requestUrl = action.requestUrl,
                reloadComponent = action.reloadComponent || false,
                payload;

            payload = composePayload(action.payload || []);
            this.loader.show();
            sendRequest(requestUrl, payload, reloadComponent)
                .done(function (response) {
                    messageManager.addSuccessMessage(response.message, 5000);
                    if (_.isArray(action.clear)) {
                        self.clear(action.clear);
                    }
                })
                .always(function () {
                    self.loader.hide();
                });
        },

        /**
         * Clear value after success request
         *
         * @param components
         * @private
         */
        clear: function(components) {
            _.each(components, function (componentName) {
                var component = registry.get(componentName);
                if (component) {
                    component.reset();
                }
            }, this);
        },

        /**
         * Init block loader
         * @private
         */
        initLoader: function () {
            this.loader = {};
            blockLoader(this.loader, this.blockLoaderSelector);
        }
    });
});
