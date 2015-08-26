module.exports = {

    data: window.$data,

    methods: {

        save: function (e) {
            e.preventDefault();

            this.$http.post('user/profile/save', {user: this.user}, function () {
                this.$notify('Profile Updated', 'success');
            }).error(function (error) {
                this.$notify(error, 'danger');
            });
        }

    }

};

$(function () {

    new Vue(module.exports).$mount('#user-profile');

});
