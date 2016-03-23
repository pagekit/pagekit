module.exports = {

    el: '#user-reset',

    data: {
        email: '',
        error: '',
        success: ''
    },

    methods: {

        submit: function () {

            this.error = null;

            this.$http.post('user/resetpassword/request', { email: this.email }).then(function (res) {
                    this.success = res.data.message;
                }, function (error) {
                    this.error = error.data;
                }
            );
        }

    }

};

Vue.ready(module.exports);
