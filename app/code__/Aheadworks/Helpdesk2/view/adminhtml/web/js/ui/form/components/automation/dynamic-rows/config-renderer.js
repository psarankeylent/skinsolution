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
            valueFieldName: 'config',
            imports: {
                configTypes: '${ $.provider }:value_mapper.actions.config_types',
                configOptions: '${ $.provider }:value_mapper.actions.config_options',
                currentAction: '${ $.actionProvider }:value',
                currentEvent: '${ $.eventProvider }:value'
            },
            listens: {
                currentAction: 'onChangeHandler',
                currentEvent: 'onChangeHandler'
            },
            configElementTemplate: {
                boolean: {
                    component: 'Magento_Ui/js/form/element/single-checkbox',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    prefer: 'toggle',
                    additionalClasses: 'column config boolean',
                    valueMap: {
                        'true': '1',
                        'false': '0'
                    },
                    default: '0'
                },
                multiselect: {
                    component: 'Magento_Ui/js/form/element/multiselect',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    additionalClasses: 'column config multiselect',
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
         * On event change handler
         */
        onChangeHandler: function () {
            var config,
                configType = this._getConfigType(),
                type,
                option;

            this._removeAllElements();

            if (configType) {
                config = this.configOptions[configType] ? this.configOptions[configType] : false;
                if (config) {
                    type = this.configElementTemplate[config.type];
                    option = {
                        options: config.options || {},
                        name: Math.random() + this.valueFieldName,
                        parent: this.name,
                        dataScope: 'config.' + config.value,
                        label: config.label || '',
                        labelVisible: true
                    };

                    layout([utils.extend({}, type, option)]);
                }
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
         * Get config type
         *
         * @return {Boolean|String}
         * @private
         */
        _getConfigType: function () {
            return this.configTypes[this.currentEvent]
                && this.configTypes[this.currentEvent][this.currentAction]
                ? this.configTypes[this.currentEvent][this.currentAction] : false;
        }
    });
});
