module.exports = {

    el: '#site',

    data: function () {
        return _.merge({
            edit: undefined,
            menu: this.$session.get('site.menu', {}),
            menus: [],
            nodes: [],
            tree: false,
            selected: []
        }, window.$data);
    },

    created: function () {
        this.Menus = this.$resource('api/site/menu{/id}');
        this.Nodes = this.$resource('api/site/node{/id}');

        var vm = this;
        this.load().then(function () {
            vm.$set('menu', _.find(vm.menus, 'id', vm.$get('menu.id')) || vm.menus[0]);
        });
    },

    ready: function () {

        var vm = this;

        UIkit.nestable(this.$els.nestable, {
            maxDepth: 20,
            group: 'site.nodes'
        }).on('change.uk.nestable', function (e, nestable, el, type) {

            if (type && type !== 'removed') {

                vm.Nodes.save({id: 'updateOrder'}, {
                    menu: vm.menu.id,
                    nodes: nestable.list()
                }).then(vm.load, function () {
                    this.$notify('Reorder failed.', 'danger');
                });
            }
        });

    },

    methods: {

        load: function () {

            var vm = this;
            return Vue.Promise.all([
                this.Menus.query(),
                this.Nodes.query()
            ]).then(function (responses) {

                vm.$set('menus', responses[0].data);
                vm.$set('nodes', responses[1].data);
                vm.$set('selected', []);

                if (!_.find(vm.menus, 'id', vm.$get('menu.id'))) {
                    vm.$set('menu', vm.menus[0]);
                }

            }, function () {
                vm.$notify('Loading failed.', 'danger');
            });
        },

        isActive: function (menu) {
            return this.menu && this.menu.id === menu.id;
        },

        selectMenu: function (menu) {

            this.$set('selected', []);
            this.$set('menu', menu);
            this.$session.set('site.menu', menu);

        },

        removeMenu: function (menu) {
            this.Menus.delete({id: menu.id}).finally(this.load);
        },

        editMenu: function (menu) {

            if (!menu) {
                menu = {
                    id: '',
                    label: ''
                };
            }

            this.$set('edit', _.merge({positions: []}, menu));

            this.$refs.modal.open();
        },

        saveMenu: function (menu) {

            this.Menus.save({menu: menu}).then(this.load, function (res) {
                this.$notify(res.data, 'danger');
            });

            this.cancel();
        },

        getMenu: function (position) {
            return _.find(this.menus, function (menu) {
                return _.contains(menu.positions, position);
            });
        },

        cancel: function () {
            this.$refs.modal.close();
        },

        status: function (status) {

            var nodes = this.getSelected();

            nodes.forEach(function (node) {
                node.status = status;
            });

            this.Nodes.save({id: 'bulk'}, {nodes: nodes}).then(function () {
                this.load();
                this.$notify('Page(s) saved.');
            });
        },

        moveNodes: function (menu) {

            var nodes = this.getSelected();

            nodes.forEach(function (node) {
                node.menu = menu;
            });

            this.Nodes.save({id: 'bulk'}, {nodes: nodes}).then(function () {
                this.load();
                this.$notify(this.$trans('Pages moved to %menu%.', {
                    menu: _.find(this.menus.concat({
                        id: 'trash',
                        label: this.$trans('Trash')
                    }), 'id', menu).label
                }));
            });
        },

        removeNodes: function () {

            if (this.menu.id !== 'trash') {

                var nodes = this.getSelected();

                nodes.forEach(function (node) {
                    node.status = 0;
                });

                this.moveNodes('trash');

            } else {
                this.Nodes.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
                    this.load();
                    this.$notify('Page(s) deleted.');
                });
            }
        },

        getType: function (node) {
            return _.find(this.types, 'id', node.type);
        },

        getSelected: function () {
            return this.nodes.filter(function (node) {
                return this.isSelected(node);
            }, this);
        },

        isSelected: function (node, children) {

            if (_.isArray(node)) {
                return _.every(node, function (node) {
                    return this.isSelected(node, children);
                }, this);
            }

            return this.selected.indexOf(node.id) !== -1 && (!children || !this.tree[node.id] || this.isSelected(this.tree[node.id], true));
        },

        toggleSelect: function (node) {

            var index = this.selected.indexOf(node.id);

            if (index == -1) {
                this.selected.push(node.id);
            } else {
                this.selected.splice(index, 1);
            }
        }

    },

    computed: {

        showDelete: function () {
            return this.showMove && _.every(this.getSelected(), function (node) {
                    return !(this.getType(node) || {})['protected'];
                }, this);
        },

        showMove: function () {
            return this.isSelected(this.getSelected(), true);
        }

    },

    watch: {

        'menu + nodes': {
            handler: function () {
                this.$set('tree', _(this.nodes).filter({menu: this.menu.id}).sortBy('priority').groupBy('parent_id').value());
            },
            deep: true
        }

    },

    filters: {

        label: function (id) {
            return _.result(_.find(this.menus, 'id', id), 'label');
        },

        protected: function (types) {
            return _.reject(types, 'protected', true);
        },

        trash: function (menus) {
            return _.reject(menus, 'id', 'trash');
        },

        divided: function (menus) {
            return _.reject(menus, 'fixed', true).concat({divider: true}, _.filter(menus, 'fixed', true))
        }

    },

    components: {

        node: {

            name: 'node',
            props: ['node', 'tree'],
            template: '#node',

            computed: {

                isFrontpage: function () {
                    return this.node.url === '/';
                },

                type: function () {
                    return this.$root.getType(this.node) || {};
                }

            },

            methods: {

                setFrontpage: function () {
                    this.$root.Nodes.save({id: 'frontpage'}, {id: this.node.id}, function () {
                        this.$root.load();
                        this.$notify('Frontpage updated.');
                    });
                },

                toggleStatus: function () {

                    this.node.status = this.node.status === 1 ? 0 : 1;

                    this.$root.Nodes.save({id: this.node.id}, {node: this.node}).then(function () {
                        this.$root.load();
                        this.$notify('Page saved.');
                    });
                }
            }
        }

    }

};

Vue.ready(module.exports);
