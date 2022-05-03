var config = {
    map: {
        '*': {
            amasty_acart_test: 'Amasty_Acart/js/test'
        }
    },

    shim: {
        'Amasty_Acart/js/grid/reports/charts': {
            deps: ['Amasty_Acart/vendor/amcharts/core.min']
        },
        'Amasty_Base/js/lib/es6-collections': {
            deps: ['Amasty_Acart/vendor/amcharts/plugins/polyfill.min']
        },

        'Amasty_Acart/vendor/amcharts/core.min': {
            deps: ['Amasty_Base/js/lib/es6-collections']
        },

        'Amasty_Acart/vendor/amcharts/charts.min': {
            deps: ['Amasty_Acart/vendor/amcharts/core.min']
        },

        'Amasty_Acart/vendor/amcharts/themes/animated.min': {
            deps: ['Amasty_Acart/vendor/amcharts/core.min']
        }
    }
};
