module.exports = {

    data: window.$data,

    ready: function() {

        this.user.roles = this.user.roles.map(function(role) {
            return String(role);
        });
    },

    watch: {

        'user.status': function (status) {
            if (typeof status === 'string') {
                this.user.status = parseInt(status);
            }
        }

    },

    computed: {

        isNew: function () {
            return !this.user.access && this.user.status;
        }

    },

    methods: {

        save: function (e) {
            e.preventDefault();

            this.$resource('api/user/:id').save({id: this.user.id}, {user: this.user, password: this.password}, function (data) {

                if (!this.user.id) {
                    window.history.replaceState({}, '', this.$url('admin/user/edit', {id: data.user.id}))
                }

                this.$set('user', data.user);

                UIkit.notify(this.$trans('User saved.'));

            }).error(function (data) {
                UIkit.notify(data, 'danger');
            });
        }

    }

};

$(function () {

    new Vue(module.exports).$mount('#user-edit');

});
