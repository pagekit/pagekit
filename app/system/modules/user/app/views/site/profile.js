module.exports = {

    data: window.$data,

    methods: {

        save: function (e) {
            e.preventDefault();

            this.$set('message', '');
            this.$set('error', '');

            this.$http.post('user/profile/save', {user: this.user}, function () {
                this.message = this.$trans('Profile Updated');
            }).error(function (error) {
                this.error = error;
            });
        }

    }

};

$(function () {

    new Vue(module.exports).$mount('#user-profile');

});
