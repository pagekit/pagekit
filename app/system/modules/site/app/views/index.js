module.exports = Vue.extend({

    data: function () {
        return _.merge({
            menu: undefined,
            menus: [],
            edit: undefined,
            nodes: [],
            tree: [],
            selected: []
        }, window.$data);
    },

    created: function () {
        this.Menus = this.$resource('api/site/menu/:id:label', {}, {update: {method: 'PUT'}});
        this.Nodes = this.$resource('api/site/node/:id');

        this.load();
    },

    methods: {

        load: function () {
            return this.Menus.query(function (data) {
                this.$set('menus', data);
            });
        },

        isActive: function (menu) {
            return this.menu && this.menu.id === menu.id;
        },

        selectMenu: function (menu) {
            this.$set('menu', menu)
        },

        editMenu: function (menu) {

            var edit = _.extend({}, menu || {label: '', id: ''});
            edit.oldId = edit.id;

            this.$set('edit', edit);

            this.modal = UIkit.modal(this.$$.modal);
            this.modal.show();
        },

        saveMenu: function (e) {

            if (e) e.preventDefault();

            this.Menus[this.edit.oldId ? 'update' : 'save']({label: this.edit.label}, this.edit, function () {
                this.load();
                this.$set('menu.id', this.edit.id);
                this.cancel();
            }).error(function (msg) {
                UIkit.notify(msg, 'danger');
            });

        },

        removeMenu: function (menu) {
            this.Menus.delete({id: menu.id}, this.load);
        },

        cancel: function (e) {
            if (e) e.preventDefault();
            this.$set('edit', null);
            this.modal.hide();
        },

        setFrontpage: function () {

            var frontpage = this.selected[0];

            this.Nodes.save({id: 'frontpage'}, {id: frontpage}, function () {
                this.load();
                this.$set('frontpage', frontpage);
                UIkit.notify('Frontpage set.');
            });
        },

        status: function (status) {

            var nodes = this.getSelected();

            nodes.forEach(function (node) {
                node.status = status;
            });

            this.Nodes.save({id: 'bulk'}, {nodes: nodes}, function () {
                this.load();
                UIkit.notify('Page(s) saved.');
            });
        },

        toggleStatus: function (node) {

            node.status = node.status === 1 ? 0 : 1;

            this.Nodes.save({id: node.id}, {node: node}, function () {
                this.load();
                UIkit.notify('Page saved.');
            });
        },

        moveNodes: function (menu) {

            var nodes = this.getSelected();

            nodes.forEach(function (node) {
                node.menu = menu;
            });

            this.Nodes.save({id: 'bulk'}, {nodes: nodes}, function () {
                this.load();
                UIkit.notify(this.$trans('Pages moved to trash.'));
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
                this.Nodes.delete({id: 'bulk'}, {ids: this.selected}, function () {
                    this.load();
                    UIkit.notify('Page(s) deleted.');
                });
            }
        },

        getType: function (node) {
            return _.find(this.types, {id: node.type});
        },

        getSelected: function () {
            return this.nodes.filter(function (node) {
                return this.isSelected(node);
            }.bind(this));
        },

        isSelected: function (node, children) {

            if (_.isArray(node)) {
                return _.every(node, function (node) {
                    return this.isSelected(node, children);
                }.bind(this))
            }

            return this.selected.indexOf(node.id.toString()) !== -1 && (!children || !this.tree[node.id] || this.isSelected(this.tree[node.id], true));
        }

    },

    computed: {

        showDelete: function () {
            return this.showMove && _.every(this.getSelected(), function (node) {
                    return !(this.getType(node) || {})['protected'];
                }.bind(this));
        },

        showMove: function () {
            return this.isSelected(this.getSelected(), true);
        }

    },

    watch: {

        menu: function () {

            this.$set('selected', []);

            return this.Nodes.query({menu: this.$get('menu.id')}, function (nodes) {
                this.$set('nodes', nodes);
                this.$set('tree', _(nodes).sortBy('priority').groupBy('parentId').value());
            });
        },

        menus: function (menus) {
            this.selectMenu(_.find(menus, {id: this.$get('menu.id')}) || menus[0]);
        },

        nodes: function () {

            var vm = this;

            // TODO this is still buggy
            UIkit.nestable(this.$$.nestable, {maxDepth: 20, group: 'site.nodes'}).off('change.uk.nestable').on('change.uk.nestable', function (e, nestable, el, type) {

                if (type && type !== 'removed') {
                    vm.Nodes.save({id: 'updateOrder'}, {menu: vm.menu.id, nodes: nestable.list()}, function () {

                        // @TODO reload everything on reorder really needed?

                        vm.load().success(function () {
                            el.remove();
                        });

                        UIkit.notify(this.$trans('Order updated.'));
                    });
                }
            });
        }

    },

    validators: {

        unique: function (value) {
            var menu = _.find(this.menus, {id: value});
            return !menu || this.edit.oldId === menu.id;
        }

    },

    filters: {

        protected: function (types) {
            return _.reject(types, {protected: true});
        },

        trash: function (menus) {
            return _.reject(menus, {id: 'trash'});
        }

    },

    components: {

        node: {

            inherit: true,
            template: '#node',

            computed: {

                url: function () {
                    return this.$url(this.isFrontpage ? '' : this.node.path.replace(/^\/+/, ''))
                },

                isFrontpage: function () {
                    return this.node.id == this.frontpage;
                }

            }
        }
    }

});

$(function () {

    (new module.exports()).$mount('#site');

});
