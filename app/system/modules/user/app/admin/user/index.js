jQuery(function ($) {

    Vue.directive('sorter', {

        bind: function () {

            this.dir    = 'desc';
            this.active = false;

            this.el.style.cursor = 'pointer';

            this.el.addEventListener('click', function(){

                this.dir    = (this.dir == 'asc' && this.active) ? 'desc':'asc';
                this.active = true;
                this.vm.$set(this.expression, [this.arg, this.dir].join(' '));

            }.bind(this));
        },

        update: function (data) {

            var parts = data.split(' '),
                field = parts[0],
                dir   = parts[1] || 'asc';

            if (field != this.arg) {
                this.active = false;
            }

            this.el.classList[this.active ? 'add':'remove']('uk-text-danger');
        }
    });

    var vm = new Vue({

        el: '#js-user',

        data: $.extend(window.$data, {
            count: ''
        }),

        created: function () {

            this.resource = this.$resource('api/user/:id');
            this.config.filter = $.extend({status: '', role: '', sort:''}, this.config.filter ? this.config.filter : {});

            this.$watch('config.page', this.load, true, true);
            this.$watch('config.filter', function () { this.load(0); }, true);
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
                    vm.load();
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
                });
            },

            status: function (status) {

                var users = this.getSelected();

                users.forEach(function (user) {
                    user.status = status;
                });

                this.resource.save({ id: 'bulk' }, { users: users }, function (data) {
                    vm.load();
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
                });
            },

            remove: function () {
                this.resource.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                    vm.load();
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
                return Vue
                    .filter('toArray')(user.roles)
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
                    vm.$set('users', data.users);
                    vm.$set('pages', data.pages);
                    vm.$set('count', data.count);
                    vm.$set('config.page', page);
                    vm.$set('selected', []);
                });
            },

            getSelected: function () {
                return this.users.filter(function (user) {
                    return vm.selected.indexOf(user.id.toString()) !== -1;
                });
            }

        }

    });

});
