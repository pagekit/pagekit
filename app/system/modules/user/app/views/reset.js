module.exports = {

    el: '#user-reset',

    data: {
        email: '',
        message: '',
        success: false,
        form: {}
    },

    methods: {

        submit: function () {

            this.message = '';
            this.$http.post('user/resetpassword/request', { email: this.email }).then(function (res) {
                    this.message = res.data.message;
                    this.success = true;
                }, function (error) {
                    this.message = error.data;
                    this.success = false;
                }
            );
        }

    }

};

Vue.ready(module.exports);
