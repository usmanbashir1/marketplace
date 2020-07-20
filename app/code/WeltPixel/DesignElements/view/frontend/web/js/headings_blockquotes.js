define(['jquery', 'Morphext'], function ($) {
    "use strict";

    var SEMICOLONHEADINGS = SEMICOLONHEADINGS || {};

    SEMICOLONHEADINGS.widget = {
        init: function () {
            SEMICOLONHEADINGS.widget.textRotater();
        },
        
        textRotater: function(){
            var $textRotaterEl = $('.text-rotater');
            if( $textRotaterEl.length > 0 ){
                    $textRotaterEl.each(function(){
                        var element = $(this),
                                trRotate = $(this).attr('data-rotate'),
                                trSpeed = $(this).attr('data-speed'),
                                trSeparator = $(this).attr('data-separator');

                        if (!trRotate) {
                            trRotate = "fade";
                        }
                        if (!trSpeed) {
                            trSpeed = 1200;
                        }
                        if (!trSeparator) {
                            trSeparator = ",";
                        }

                        var tRotater = $(this).find('.t-rotate');

                        tRotater.Morphext({
                            animation: trRotate,
                            separator: trSeparator,
                            speed: Number(trSpeed)
                        });
                    });
            }
        }
        
    };

    return SEMICOLONHEADINGS;
});
