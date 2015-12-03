module.exports = {

    el: '#user-registration',

    data: {
        user: {},
        error: null
    },

    methods: {

        submit: function () {
            this.$http.post('user/registration/register', {user: this.user}, function (data) {
                window.location.replace(data.redirect);
            }).error(function (error) {
                this.error = error;
            });
        }

    }

};

Vue.ready(module.exports);
