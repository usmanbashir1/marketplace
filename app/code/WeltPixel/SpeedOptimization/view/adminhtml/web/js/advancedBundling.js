define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui',
    'mage/translate'
], function ($, alert) {
    "use strict";

    var AdvancedBundling = AdvancedBundling || {};

    var step1Button = $('#ab_step_1'),
        step2Button = $('#ab_step_2'),
        step3Button = $('#ab_step_3'),
        themeLocaleInput = $('#theme_locales_path'),
        websiteSwitcher = $('#website_switcher'),
        storeSwitcher = $('#store_switcher'),
        formKey = $('#api_form_key');

    AdvancedBundling.initialize = function (postUrl) {
        var that = this;
        $('.ab-step-1').show();

        $(step1Button).click(function() {
            $.ajax({
                showLoader: true,
                url: postUrl,
                data: {
                    'form_key' : formKey.val(),
                    'step' : 1,
                    'store' : storeSwitcher.val(),
                    'website' : websiteSwitcher.val()
                },
                type: "POST",
                dataType: 'json'
            }).done(function () {
                $('.ab-steps').hide();
                $('.ab-step-2').show();
            });
        });

        $(step2Button).click(function() {
            if (!$(themeLocaleInput).val()) {
                alert({content: $.mage.__('Please select theme and locale combination')});
                return false;
            }

            $.ajax({
                showLoader: true,
                url: postUrl,
                data: {
                    'form_key' : formKey.val(),
                    'step' : 2,
                    'themelocales': $(themeLocaleInput).val()
                },
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                $('.ab-step-3 .prev-step-result').html(data.msg.join('<br/>')).show();
                $('.ab-steps').hide();
                $('.ab-step-3').show();
                if (data.error) {
                    $('.ab-step-3 .prev-step-result').addClass('step-error');
                    $('.ab-step-3 .no-error').hide();
                } else {
                    $('.ab-step-3 .no-error').show();
                }
            });
        });

        $(step3Button).click(function() {
            $.ajax({
                showLoader: true,
                url: postUrl,
                data: {
                    'form_key' : formKey.val(),
                    'step' : 3,
                    'themelocales': $(themeLocaleInput).val()
                },
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                $('.ab-step-4 .prev-step-result').html(data.msg.join('<br/>')).show();
                $('.ab-steps').hide();
                $('.ab-step-4').show();
            });
        });


    };

    return AdvancedBundling;
});