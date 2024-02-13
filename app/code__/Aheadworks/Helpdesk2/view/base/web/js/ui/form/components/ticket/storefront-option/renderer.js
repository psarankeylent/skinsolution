define([
    'underscore',
    'uiCollection',
    'mageUtils',
    'uiLayout',
    'mage/translate'
], function (_, uiCollection, utils, layout, $t) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            imports: {
                options: '${ $.configProvider }:data.storefront_options',
                departmentSelector: '${ $.departmentProvider }:value'
            },
            listens: {
                departmentSelector: 'onDepartmentChange'
            },
            modules: {
                departmentElement: '${ $.departmentProvider }'
            },
            optionTemplate: {
                field: {
                    component: 'Magento_Ui/js/form/element/abstract',
                    template: 'ui/form/field',
                    provider: '${ $.provider }'
                },
                dropdown: {
                    component: 'Magento_Ui/js/form/element/select',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    caption: $t('-- Please Select --')
                }
            }
        },

        /**
         * Create custom option instance
         *
         * @param {Number} department
         */
        onDepartmentChange: function (department) {
            var type,
                options = this.options[department],
                isRenderAllowed = true,
                departmentObject = this.departmentElement().getOption(department);

            if (this.departmentElement().isGuestCustomer && departmentObject) {
                isRenderAllowed = Boolean(departmentObject.is_allow_guest)
            }

            this._removeAllOptions();
            if (options && isRenderAllowed) {
                _.each(options, function (option) {
                    type = this.optionTemplate[option.type];
                    option = {
                        label: option.label,
                        validation: this._prepareValidation(option),
                        options: option.options || {},
                        name: option.id,
                        parent: this.name,
                        dataScope: 'options.' + option.id
                    };

                    layout([utils.extend({}, type, option)]);
                }, this);
            }
        },

        /**
         * Prepare validation
         *
         * @param {Object} option
         * @return {Object}
         * @private
         */
        _prepareValidation: function (option) {
            var validation = {};

            if (option.is_required) {
                validation['required-entry'] = option.is_required;
            }

            return validation;
        },

        /**
         * Remove all options
         *
         * @private
         */
        _removeAllOptions: function () {
            if (_.size(this.elems()) > 0) {
                this.destroyChildren();
            }
        }
    });
});
