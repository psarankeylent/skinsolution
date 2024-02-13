define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/select'
], function ($, _, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            modules: {
                target: '${ $.targetName }'
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();

            if (_.isEmpty(this.options())) {
                this.visible(false);
            }

            return this;
        },

        /**
         * Handler for change value
         */
        onUpdate: function() {
            var option,
                id = this.value(),
                tinyEditor
            if (id) {
                option = this.getOption(id);

                tinyEditor = window.tinyMCE.get(this.target().wysiwygId);
                tinyEditor.execCommand('mceInsertClipboardContent', false, {
                    content: option.response
                });
                tinyEditor.focus();

                this.clear();
            }
        },
    });
});
