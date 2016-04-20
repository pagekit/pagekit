module.exports = {

    name: 'user-index',

    el: '#users',

    data: function () {
        return _.merge({
            users: false,
            config: {
              filter: this.$session.get('user.filter', {order: 'username asc'})
            },
            pages: 0,
            count: '',
            selected: []
        }, window.$data);
    },

    ready: function () {
        this.resource = this.$resource('api/user{/id}');
        this.$watch('config.page', this.load, {immediate: true});
    },

    watch: {

        'config.filter': {
            handler: function (filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }

                this.$session.set('user.filter', filter);
            },
            deep: true
        }

    },

    computed: {

        statuses: function () {

            var options = [{text: this.$trans('New'), value: 'new'}].concat(_.map(this.config.statuses, function (status, id) {
                return {text: status, value: id};
            }));

            return [{label: this.$trans('Filter by'), options: options}];
        },

        roles: function () {

            var options = this.config.roles.map(function (role) {
                return {text: role.name, value: role.id};
            });

            return [{label: this.$trans('Filter by'), options: options}];
        }

    },

    methods: {

        active: function (user) {
            return this.selected.indexOf(user.id) != -1;
        },

        save: function (user) {
            this.resource.save({id: user.id}, {user: user}).then(function () {
                this.load();
                this.$notify('User saved.');
            }, function (res) {
                this.load();
                this.$notify(res.data, 'danger');
            });
        },

        status: function (status) {

            var users = this.getSelected();

            users.forEach(function (user) {
                user.status = status;
            });

            this.resource.save({id: 'bulk'}, {users: users}).then(function () {
                this.load();
                this.$notify('Users saved.');
            }, function (res) {
                this.load();
                this.$notify(res.data, 'danger');
            });
        },

        remove: function () {
            this.resource.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
                this.load();
                this.$notify('Users deleted.');
            }, function (res) {
                this.load();
                this.$notify(res.data, 'danger');
            });
        },

        toggleStatus: function (user) {
            user.status = !!user.status ? 0 : 1;
            this.save(user);
        },

        showVerified: function (user) {
            return this.config.emailVerification && user.data.verified;
        },

        showRoles: function (user) {
            return _.reduce(user.roles, function (roles, id) {
                var role = _.find(this.config.roles, 'id', id);
                if (id !== 2 && role) {
                    roles.push(role.name);
                }
                return roles;
            }, [], this).join(', ');
        },

        load: function () {
            this.resource.query({filter: this.config.filter, page: this.config.page}).then( function (res) {
                var data = res.data;

                this.$set('users', data.users);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('selected', []);
            }, function () {
                this.$notify('Loading failed.', 'danger');
            });
        },

        getSelected: function () {
            return this.users.filter(function (user) {
                return this.selected.indexOf(user.id) !== -1;
            }, this);
        }

    }

};

Vue.ready(module.exports);
