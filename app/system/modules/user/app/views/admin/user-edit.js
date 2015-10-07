window.User = module.exports = {

    data: function() {
        return _.merge({password: ''}, window.$data);
    },

    created: function () {

        var sections = [];

        _.forIn(this.$options.components, function (component, name) {

            var options = component.options || {};

            if (options.section) {
                sections.push(_.extend({name: name, priority: 0}, options.section));
            }

        });

        this.$set('sections', _.sortBy(sections, 'priority'));

    },

    ready: function () {
        this.tab = UIkit.tab(this.$$.tab, {connect: this.$$.content});
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
                    window.history.replaceState({}, '', this.$url.route('admin/user/edit', {id: data.user.id}))
                }

                this.$set('user', data.user);

                this.$notify('User saved.');

            }).error(function (data) {
                this.$notify(data, 'danger');
            });
        }

    },

    components: {

        'settings': require('../../components/user-settings.vue')

    }

};

$(function () {

    new Vue(module.exports).$mount('#user-edit');

});
