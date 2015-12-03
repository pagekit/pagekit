window.User = {

    el: '#user-edit',

    data: function () {
        return _.extend({sections: [], form: {}}, window.$data);
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
        this.tab = UIkit.tab(this.$els.tab, {connect: this.$els.content});
    },

    methods: {

        save: function () {

            var data = {user: this.user};

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

    components: {

        settings: require('../../components/user-settings.vue')

    }

};

Vue.ready(window.User);
