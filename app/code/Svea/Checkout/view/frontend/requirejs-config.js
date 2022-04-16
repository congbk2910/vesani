var config = {
    map: {
        '*': {
            sveaCheckout: 'Svea_Checkout/js/checkout',
            sveaShippingMethod: 'Svea_Checkout/js/sveashippingmethod',
            sveaProductCampaign: 'Svea_Checkout/js/product-campaign',
            'Magento_Reward/js/action/set-use-reward-points': 'Svea_Checkout/js/action/set-use-reward-points'
        }
    },
    paths: {
        slick: 'Svea_Checkout/js/lib/slick.min'
    },
    shim: {
        slick: {
            deps: ['jquery']
        }
    }
};
