define(['jquery', 'jRespond'], function ($) {
    "use strict";

    var $body = $('body');
    var SEMICOLONBASE = SEMICOLONBASE || {};

    SEMICOLONBASE.widget = {
        init: function (options) {
            this.breakpoints = options.breakpoints;
            SEMICOLONBASE.widget.responsiveWpClasses();
        },
        responsiveWpClasses: function(){
            var jRes = jRespond([
                    {
                            label: 'xxs',
                            enter: this.breakpoints.xxs.enter,
                            exit: this.breakpoints.xxs.exit
                    },{
                            label: 'xs',
                            enter: this.breakpoints.xs.enter,
                            exit: this.breakpoints.xs.exit
                    },{
                            label: 's',
                            enter: this.breakpoints.s.enter,
                            exit: this.breakpoints.s.exit
                    },{
                            label: 'm',
                            enter: this.breakpoints.m.enter,
                            exit: this.breakpoints.m.exit
                    },{
                            label: 'l',
                            enter: this.breakpoints.l.enter,
                            exit: this.breakpoints.l.exit
                    },{
                            label: 'xl',
                            enter: this.breakpoints.xl.enter,
                            exit: this.breakpoints.xl.exit
                    }
            ]);
            jRes.addFunc([
                    {
                            breakpoint: 'xxs',
                            enter: function() { $body.addClass('wp-device-xxs'); },
                            exit: function() { $body.removeClass('wp-device-xxs'); }
                    },{
                            breakpoint: 'xs',
                            enter: function() { $body.addClass('wp-device-xs'); },
                            exit: function() { $body.removeClass('wp-device-xs'); }
                    },{
                            breakpoint: 's',
                            enter: function() { $body.addClass('wp-device-s'); },
                            exit: function() { $body.removeClass('wp-device-s'); }
                    },{
                            breakpoint: 'm',
                            enter: function() { $body.addClass('wp-device-m'); },
                            exit: function() { $body.removeClass('wp-device-m'); }
                    },{
                            breakpoint: 'l',
                            enter: function() { $body.addClass('wp-device-l'); },
                            exit: function() { $body.removeClass('wp-device-l'); }
                    },{
                            breakpoint: 'xl',
                            enter: function() { $body.addClass('wp-device-xl'); },
                            exit: function() { $body.removeClass('wp-device-xl'); }
                    }
            ]);
        }
    };

    return SEMICOLONBASE;
});
