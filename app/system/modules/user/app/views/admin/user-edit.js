module.exports = {

    data: window.$data,

    ready: function() {
        UIkit.tab(this.$$.tab, {connect: this.$$.content});

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
        },

        sections: function () {

            var sections = [];

            _.forIn(this.$options.components, function (component, name) {

                var options = component.options || {}, section = options.section;

                if (section) {
                    section.name = name;
                    sections.push(section);
                }

            });

            return sections;
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

    },

    mixins: [window.Users]

};

$(function () {

    new Vue(module.exports).$mount('#user-edit');

});
