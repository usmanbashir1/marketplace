define([], function () {
    'use strict';
    return function (Registration) {
        return Registration.extend({
            getDescription: function() {
                return this.description;
            },
            getEmailLabel: function() {
                return this.emailLabel;
            },
            getAfterCreationMessage: function() {
                return this.afterCreationLabel;
            }
        });
    }
});
