var config = {
    map: {
        '*': {
            mute_migrate: 'WeltPixel_FrontendOptions/js/mute_migrate'
        }
    },
    shim: {
        'jquery/jquery-migrate': {
            deps: ['jquery','mute_migrate']
        }
    }
};
