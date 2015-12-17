module.exports = {

    el: '#users',

    data: function () {
        return _.merge({
            users: false,
            pages: 0,
            count: '',
            selected: []
        }, window.$data);
    },

    created: function () {

        this.resource = this.$resource('api/user/:id');
        this.config.filter = _.extend({order: 'name asc'}, this.config.filter);

    },

    watch: {

        'config.page': 'load',

        'config.filter': {
            handler: function () {
                this.load(0);
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

        load: function (page) {

            page = page !== undefined ? page : this.config.page;

            this.resource.query({filter: this.config.filter, page: page}).then( function (res) {
                var data = res.data;

                this.$set('users', data.users);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('config.page', page);
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
