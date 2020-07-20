define(
    [
        'underscore',
        'Magento_Ui/js/form/element/select',
        'ko',
        'jquery'
    ],
    function (_, Select, ko, $) {
        'use strict';

        return Select.extend(
            {
                defaults: {
                    positions: [
                        {name: '1', cssClass: ''},
                        {name: '2', cssClass: ''},
                        {name: '3', cssClass: ''},
                        {name: '4', cssClass: ''},
                        {name: '5', cssClass: ''},
                        {name: '6', cssClass: ''},
                        {name: '7', cssClass: ''},
                        {name: '8', cssClass: ''},
                        {name: '9', cssClass: ''}
                    ]
                },

                initialize: function() {
                    this._super();
                    this.initPositionBox();
                    return this;
                },

                /**
                 * init observers
                 */
                initObservable: function () {
                    this._super().observe('positions');
                    return this;
                },


                /**
                 * Mark active element
                 */
                initPositionBox: function() {
                    var that = this;
                    if (that.value()) {
                        _(that.positions()).each(function (positionElement, i) {
                            positionElement.cssClass = '';
                            if (that.value() == positionElement.name) {
                                positionElement.cssClass = 'active';
                            }
                        });
                    } else {
                        that.positions()[0].cssClass = 'active';
                    }
                },

                /**
                 * Update the new value
                 * @param item
                 */
                updatePosition: function(item) {
                    this.value(item.name);
                    var that = this;
                    _(that.positions()).each(function(positionElement, i){
                        var itemClass = '';
                        if  (positionElement.name == item.name) {
                            itemClass = 'active';
                        }
                        that.positions.splice(positionElement.name - 1, 1, {
                            'name': positionElement.name,
                            'cssClass' : itemClass
                        });
                    });
                },
                /**
                 * hide table box if value 10 (other position selected)
                 * @param item
                 * @returns {boolean}
                 */
                isDisabled: function(item) {
                    this.value(item.name);
                    var that = this;
                    var product_page_position = $('select[name=product_page_position]').val();
                    if(that.value() == 10 && product_page_position != 1) {
                        $('div[data-index="product_position"]').hide();
                    }
                }
            }
        );
    }
);