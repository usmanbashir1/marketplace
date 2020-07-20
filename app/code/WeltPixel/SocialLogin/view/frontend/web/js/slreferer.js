require([
    'jquery',
    'jquery/jquery.cookie',
    'domReady!'
], function($) {
    'use strict';

    var url = document.URL.toLowerCase();
    if (url) {
        var skip = false;
        $.each(window.skipModules, function(i, path) {
            if (url.indexOf(path) !== -1) {
                skip = true;
                return false;
            }
        });

        if (!skip) {
            $.cookie(window.queryParam, document.URL, {
                path: '/'
            });
        }
    }
});