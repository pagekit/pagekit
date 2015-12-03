module.exports = {

    el: '#user-profile',

    data: window.$data,

    methods: {

        save: function () {
            this.$http.post('user/profile/save', {user: this.user}, function () {
                this.$notify('Profile Updated', 'success');
            }).error(function (error) {
                this.$notify(error, 'danger');
            });
        }

    }

};

Vue.ready(module.exports);
