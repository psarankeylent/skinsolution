
var config = {
    map: {
        '*': {
            subscriptionsView: 'ParadoxLabs_Subscriptions/js/view',
            subscriptionsEdit: 'ParadoxLabs_Subscriptions/js/edit'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/price-options': {
                'ParadoxLabs_Subscriptions/js/product/view/price-options-mixin': true
            }
        }
    }
};
