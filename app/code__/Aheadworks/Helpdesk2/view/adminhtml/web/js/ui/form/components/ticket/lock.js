define([
    'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/single-checkbox'
], function (SingleCheckbox) {
    'use strict';

    return SingleCheckbox.extend({
        defaults: {
            isAllowedToLock: false,
            isAllowedToUnlock: false
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            var isLocked;

            this._super();
            isLocked = Boolean(JSON.parse(this.value()));
            this.isEditModeAllowed = isLocked ? this.isAllowedToUnlock : this.isAllowedToLock;

            return this;
        }
    });
});
