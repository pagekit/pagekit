jQuery(function ($) {

    var vm = new Vue({

        el: '#user-registration',

        data: {
            user: {},
            error: null
        },

        methods: {

            submit: function (e) {
                e.preventDefault();

                var self = this;

                this.$http.post('user/registration/register', {user: this.user}, function (data) {
                    window.location.replace(data.redirect);
                }).error(function (error) {
                    self.error = error;
                });
            }

        }

    });

});
