define([
    'underscore',
    'uiCollection',
    'mageUtils',
    'uiLayout'
], function (_, uiCollection, utils, layout) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            visible: true,
            valueFieldName: 'value',
            imports: {
                valueOptions: '${ $.optionSetProvider }',
                currentAction: '${ $.actionProvider }:value',
            },
            listens: {
                currentAction: 'onActionChange'
            },
            valueElementTemplate: {
                select: {
                    component: 'Magento_Ui/js/form/element/select',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    validation: {
                        'required-entry': true
                    }
                },
                textarea: {
                    component: 'Magento_Ui/js/form/element/textarea',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    rows: 5,
                    validation: {
                        'required-entry': true
                    }
                },
                tag_names: {
                    component: 'Aheadworks_Helpdesk2/js/ui/form/components/tags',
                    elementTmpl: 'Aheadworks_Helpdesk2/ui/form/element/tags',
                    provider: '${ $.provider }',
                    dataType: 'string',
                    size: 6,
                    formElement: 'multiselect',
                    validation: {
                        'required-entry': true
                    }
                }
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();
            this.observe('visible');
            return this;
        },

        /**
         * On action change handler
         *
         * @param {String} condition
         */
        onActionChange: function (condition) {
            var valueConfig = this.valueOptions[condition],
                type,
                option;

            this._removeAllElements();
            if (valueConfig) {
                type = this.valueElementTemplate[valueConfig.type];
                option = {
                    options: valueConfig.options || [],
                    name: Math.random() + this.valueFieldName,
                    parent: this.name,
                    dataScope: this.valueFieldName,
                    labelVisible: false,
                    additionalClasses: 'column value'
                };

                if (valueConfig.type === 'tag_names') {
                    option.options = this._addTagsFromValue(option.options);
                }

                layout([utils.extend({}, type, option)]);
            }
        },

        /**
         * Remove all elements
         *
         * @private
         */
        _removeAllElements: function () {
            if (_.size(this.elems()) > 0) {
                this.source.remove(this.dataScope + '.' + this.valueFieldName);
                this.destroyChildren();
            }
        },

        /**
         * Add additional tags from values
         *
         * @private
         */
        _addTagsFromValue: function (options) {
            var value = this.source.get(this.dataScope + '.' + this.valueFieldName),
                newOptions = [];

            if (_.isArray(value)) {
                _.each(value, function (tagName, index) {
                    newOptions.push({
                        'label': tagName,
                        'value': String(index + 1)
                    });
                });
            }

            _.each(options, function (tagOption) {
                newOptions.push({
                    'label': tagOption.label,
                    'value': String(newOptions.length + 1)
                });
            });

            return newOptions;
        }
    });
});
