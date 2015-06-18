var Users = {

    data: function() {
        return _.merge({
            users: false,
            pages: 0,
            count: '',
            selected: []
        }, window.$data)
    },

    created: function () {

        this.resource = this.$resource('api/user/:id');
        this.config.filter = _.extend({ search: '', status: '', role: '', order: 'name asc' }, this.config.filter);

        this.$watch('config.page', function () { this.load(); }, {immediate: true});
        this.$watch('config.filter', function () { this.load(0); }, {deep: true});
    },

    computed: {

        statuses: function () {

            var options = [{ text: this.$trans('New'), value: 'new' }].concat(_.map(this.$data.statuses, function (status, id) {
                return { text: status, value: id };
            }));

            return [{ text: this.$trans('Status'), value: '' }, { label: this.$trans('Filter by'), options: options }];
        },

        roles: function () {

            var options = this.$data.roles.map(function (role) {
                return { text: role.name, value: role.id };
            });

            return [{ text: this.$trans('Role'), value: '' }, { label: this.$trans('Filter by'), options: options }];
        }

    },

    methods: {

        active: function (user) {
            return this.selected.indexOf(user.id.toString()) != -1;
        },

        save: function (user) {
            this.resource.save({ id: user.id }, { user: user }, function (data) {
                this.load();
                UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
            });
        },

        status: function (status) {

            var users = this.getSelected();

            users.forEach(function (user) {
                user.status = status;
            });

            this.resource.save({ id: 'bulk' }, { users: users }, function (data) {
                this.load();
                UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
            });
        },

        remove: function () {

            this.resource.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                this.load();
                UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
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
            return user.roles
                .filter(function (role) {
                    return role.id != 2;
                })
                .map(function (role) {
                    return role.name;
                })
                .join(', ');
        },

        load: function (page) {

            page = page !== undefined ? page : this.config.page;

            this.resource.query({ filter: this.config.filter, page: page }, function (data) {
                this.$set('users', data.users);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('config.page', page);
                this.$set('selected', []);
            });
        },

        getSelected: function () {
            var vm = this;

            return this.users.filter(function (user) {
                return vm.selected.indexOf(user.id.toString()) !== -1;
            });
        }

    }

};

jQuery(function () {
    new Vue(Users).$mount('#users');
});
