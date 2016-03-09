module.exports = {

    el: '#user-registration',

    data: {
        user: {},
        error: null,
        success: '',
        redirect: ''
    },

    methods: {


        submit: function () {

            this.error = null;

            this.$http.post('user/registration/register', {user: this.user}).then(function (res) {

                    this.success = res.data.message;
                    this.redirect = res.data.redirect;

            }, function (error) {
                    this.error = error.data;
                }
            );
        }

    }

};

Vue.ready(module.exports);
