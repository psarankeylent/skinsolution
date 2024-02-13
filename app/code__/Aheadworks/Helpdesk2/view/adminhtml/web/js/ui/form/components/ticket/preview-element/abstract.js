define([
    'jquery',
    'underscore',
    'Aheadworks_Helpdesk2/js/action/ticket/send-request',
    'Aheadworks_Helpdesk2/js/model/compose-payload-from-source',
    'Aheadworks_Helpdesk2/js/model/block-loader',
    'Aheadworks_Helpdesk2/js/model/backend-message-manager'
], function ($, _, sendRequest, composePayload, blockLoader, messageManager) {
    'use strict';

    return {
        defaults: {
            previewMode: true,
            isEditModeAllowed: true,
            elementTmplPreview: 'Aheadworks_Helpdesk2/ui/form/element/ticket/preview/preview-element',
            hasAddons: false,
            requestUrl: '',
            payload: [],
            reloadComponent: false,
            bindAttributeMap: {}
        },

        loader: {},

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this
                ._super()
                .observe('previewMode hasAddons');

            return this;
        },

        /**
         * @inheritDoc
         */
        setInitialValue: function () {
            this._super();
            this.previewMode.subscribe(
                this.onPreviewModeChange, this
            );
            this.exchangeTemplates();
            this.initLoader();

            return this;
        },

        /**
         * Handler for change previewMode
         */
        onPreviewModeChange: function() {
            this.exchangeTemplates();
            this.rerender();
        },

        /**
         * Enable edit mode
         */
        enableEditMode: function () {
            this.previewMode(false);
            this.focused(true);
        },

        /**
         * Enable preview mode
         */
        enablePreviewMode: function () {
            if (!this.error()) {
                this.previewMode(true);
            }
        },

        /**
         * Exchange elementTmpl
         */
        exchangeTemplates: function() {
            if (this.previewMode()) {
                this.elementTmplBackup = this.elementTmpl;
                this.elementTmpl = this.elementTmplPreview;
            } else {
                this.elementTmpl = this.elementTmplBackup;
            }
        },

        /**
         * Rerender component
         */
        rerender: function () {
            this.hasAddons(!this.hasAddons());
            this.hasAddons(!this.hasAddons());
        },

        /**
         * Send request to backend
         */
        save: function () {
            var self = this,
                payload;

            payload = composePayload(this.payload);
            this.loader.show();

            return sendRequest(this.requestUrl, payload, this.reloadComponent)
                .always(function () {
                    self.loader.hide();
                })
                .done(function (response) {
                    messageManager.addSuccessMessage(response.message, 5000);
                    self.enablePreviewMode();
                });
        },

        /**
         * Init block loader
         */
        initLoader: function () {
            this.loader = {};
            blockLoader(this.loader, '[data-index=' + this.index + ']');
        },

        /**
         * Retrieve bind data attribute map
         *
         * @return {exports.defaults.bindAttributeMap|{}}
         */
        getBindAttributeMap: function () {
            var self = this,
                preparedMap = {};

            _.each(this.bindAttributeMap, function (item, index) {
                if (_.has(self, item)) {
                    preparedMap[index] = self[item];
                }
            });

            return preparedMap;
        }
    };
});
