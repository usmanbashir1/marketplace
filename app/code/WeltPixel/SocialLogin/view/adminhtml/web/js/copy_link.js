define([
    'jquery'
], function ($) {
    'use strict';
    return function(config){
        $(document).on("click", ".copy-link", function(event){
            event.preventDefault();
            var provider = jQuery(this).attr('data-provider');
            var textToCopy = $('#weltpixel_sociallogin_'+provider+'_callback');
            var tooltipElement = $('#copied_' + provider);
            textToCopy.attr('disabled', false);
            tooltipElement.fadeIn();
            textToCopy.select();
            document.execCommand("copy");
            setTimeout(function() {
                tooltipElement.fadeOut('slow');
            }, 3000);
            textToCopy.attr('disabled', true);
        });

    }

});