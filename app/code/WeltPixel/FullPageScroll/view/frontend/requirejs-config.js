var config = {
    map: {
        '*': {
            full_page: 'WeltPixel_FullPageScroll/js/jquery.fullPage',
            scrolloverflow: 'WeltPixel_FullPageScroll/js/scrolloverflow',
            fullpagescroll: 'WeltPixel_FullPageScroll/js/fullPageScroll'
        }
    },
    shim: {
        fullpagescroll: {
            deps: ['jquery', 'full_page']
        }
    }
};