var config = {
    map: {
        '*': {
            designelements_base: 'WeltPixel_DesignElements/js/designelements_base',
            designelements_default: 'WeltPixel_DesignElements/js/designelements_default',
            toggles_accordions_tabs: 'WeltPixel_DesignElements/js/toggles_accordions_tabs',
            jRespond: 'WeltPixel_DesignElements/js/canvas/jRespond',
            Morphext: 'WeltPixel_DesignElements/js/canvas/Morphext',
            headings_blockquotes: 'WeltPixel_DesignElements/js/headings_blockquotes',
            smooth_scrolling:  'WeltPixel_DesignElements/js/smooth_scrolling',
            Alert: 'WeltPixel_DesignElements/js/bootstrap/alert',
            Button: 'WeltPixel_DesignElements/js/bootstrap/button',
            Dropdown: 'WeltPixel_DesignElements/js/bootstrap/dropdown',
            testimonialsGrid:  'WeltPixel_DesignElements/js/testimonialsGrid',
            flexslider: 'WeltPixel_DesignElements/js/canvas/jquery.flexslider',
            load_flex_slider: 'WeltPixel_DesignElements/js/load_flex_slider',
            stellar: 'WeltPixel_DesignElements/js/canvas/jquery.parallax',
            load_parallax: 'WeltPixel_DesignElements/js/load_parallax',
            jquery_important: 'WeltPixel_DesignElements/js/canvas/jquery.important',
            animations:  'WeltPixel_DesignElements/js/animations',
            aos_animation:  'WeltPixel_DesignElements/js/aos',
            jquery_transition: 'WeltPixel_DesignElements/js/canvas/jquery.transition',
            btt_button:  'WeltPixel_DesignElements/js/btt_button',
        }
    },
    shim: {
        "mage/tabs": {
            deps: ['toggles_accordions_tabs']
        },
        Morphext: {
            deps: ['jquery']
        },
        toggles_accordions_tabs: {
            deps: ['jquery']
        },
        Alert: {
            deps: ['jquery']
        },
        Button: {
            deps: ['jquery']
        },
        Dropdown: {
            deps: ['jquery']
        },
        flexslider: {
            deps: ['jquery']
        },
        stellar: {
            deps: ['jquery']
        },
        jquery_important: {
            deps: ['jquery']
        },
        jquery_transition: {
            deps: ['jquery']
        }
    },
    config: {
        mixins: {
            'mage/collapsible': {
                'WeltPixel_DesignElements/js/collapsible-mixin': true
            }
        }
    }
};
