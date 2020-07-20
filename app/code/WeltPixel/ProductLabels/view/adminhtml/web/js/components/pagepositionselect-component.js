define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        /**
         * show/hide position field
         * @param id
         */
        selectOption: function(id){
            if($("#"+id).val() == 1) {
                $('div[data-index="product_position"]').show();
                $('.positions-container').show();
            } else {
                $('div[data-index="product_position"]').hide();
            }
        },
    });
});