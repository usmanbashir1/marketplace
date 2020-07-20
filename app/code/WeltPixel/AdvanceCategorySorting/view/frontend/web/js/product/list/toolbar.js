define([
    'jquery',
    'jquery-ui-modules/widget',
    'Magento_Catalog/js/product/list/toolbar'
], function($){

    $.widget('weltpixel.productListToolbarForm', $.mage.productListToolbarForm, {
        changeUrl: function (paramName, paramValue, defaultValue)
        {
            var decode = window.decodeURIComponent,
                urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters, i;

            for (i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined ?
                    decode(parameters[1].replace(/\+/g, '%20')) :
                    '';
            }

            /** get the real attr name from param */
            var paramValueArr = paramValue.split('~'),
                paramValueNew = paramValueArr[0];

            paramData[paramName] = paramValueNew;

            /** get the given direction from param */
            var directionName = this.options.direction;
            if (paramValueArr.length == 2 && paramName != directionName) {
                paramData[directionName] = paramValueArr[1];
            }

            paramData = $.param(paramData);

            location.href = baseUrl + (paramData.length ? '?' + paramData : '');
        }
    });

    return $.weltpixel.productListToolbarForm;
});
