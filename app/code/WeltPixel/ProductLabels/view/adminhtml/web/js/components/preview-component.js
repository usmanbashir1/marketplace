define(
    [
        'underscore',
        'Magento_Ui/js/form/element/abstract',
        'ko',
        'jquery'
    ],
    function (_, Abstract, ko, $) {
        'use strict';

        return Abstract.extend(
            {
                defaults: {
                    elementTmpl: 'WeltPixel_ProductLabels/component/preview',
                    textLabel: '',
                    textColor:'',
                    textBgColor:'',
                    textFontSize: '',
                    textPadding: ''
                },

                /**
                 * init observers
                 */
                initObservable: function () {
                    this._super().observe('textLabel textColor textBgColor textFontSize textPadding');
                    return this;
                }
            }
        );
    }
);