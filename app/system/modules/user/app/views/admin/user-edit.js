module.exports = {

    data: _.merge({password: ''}, window.$data),

    ready: function() {
        UIkit.tab(this.$$.tab, {connect: this.$$.content});
        UIkit.init(this.$el);

        this.user.roles = this.user.roles.map(function(role) {
            return String(role);
        });
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

            var data = {
                user: this.user,
                password: this.password
            };

            this.$broadcast('save', data);

            this.$resource('api/user/:id').save({id: this.user.id}, data, function (data) {

                if (!this.user.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/user/edit', {id: data.user.id}))
                }

                this.$set('user', data.user);

                this.$notify('User saved.');

            }).error(function (data) {
                this.$notify(data, 'danger');
            });
        }

    },

    mixins: [window.Users]

};

$(function () {

    new Vue(module.exports).$mount('#user-edit');

});
