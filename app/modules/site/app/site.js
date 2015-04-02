jQuery(function ($) {

    var vm = new Vue({

        el: '#js-site',

        data: _.merge({
            current: {},
            nodes: null,
            menus: null
        }, $data),

        created: function () {

            Vue.validators['unique'] = function(value) {
                var menu = _.find(this.menus, { id: value });
                return !menu || this.menu.oldId == menu.id;
            };

            this.Nodes = this.$resource('api/site/node/:id');
            this.Menus = this.$resource('api/site/menu/:id', {}, { 'update': { method: 'PUT' }});

            Vue.http.defaults.options = _.extend({}, Vue.http.defaults.options, { error: function (msg) {
                UIkit.notify(msg, 'danger');
            }});

            this.load();
        },

        methods: {

            select: function(node) {
                this.$set('current', node || {});
            },

            load: function() {

                this.Nodes.query(function (nodes) {
                    vm.$set('nodes', nodes);
                    vm.select(_.find(nodes, { id: vm.current.id }) || nodes[0]);
                });

                this.Menus.query(function (menus) {
                    vm.$set('menus', menus);
                });
            }

        },

        components: {

            'menu-list': {

                inherit : true,

                data: function() {
                    return { menu: {} };
                },

                methods: {

                    add: function(menu, type) {
                        vm.select({ menu: menu.id, type: type.id })
                    },

                    edit: function (menu) {

                        menu = Vue.util.extend({}, menu || { label: '', id: '' });
                        menu.oldId = menu.id;

                        if (menu.fixed) return;

                        if (!this.modal) {
                            this.modal = UIkit.modal(this.$$.modal);
                        }

                        this.modal.show();
                        this.$set('menu', menu);
                    },

                    save: function (e) {

                        if (e) {
                            e.preventDefault();
                        }

                        this.Menus[this.menu.oldId ? 'update' : 'save']({ id: this.menu.id }, this.menu, vm.load);
                        this.cancel();
                    },

                    'delete': function () {
                        this.Menus.delete({ id: this.menu.id }, vm.load);

                        this.cancel();
                    },

                    cancel: function (e) {
                        if (e) e.preventDefault();
                        this.$set('menu', {});
                        this.modal.hide();
                    }

                }
            },

            'node-list': {

                inherit: true,
                template: '<node-item v-repeat="node: children"></node-item>',

                ready: function () {

                    var self = this;

                    if (this.node) return;

                    UIkit.nestable(this.$el).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
                        if (type !== 'removed') {
                            vm.Nodes.save({ id: 'updateOrder' }, { menu: self.menu.id, nodes: nestable.list() }, vm.load);
                        }
                    });
                },

                computed: {

                    children: function() {
                        return _(this.nodes).filter({ menu: this.menu.id, parentId: this.node ? this.node.id : 0 }).sortBy('priority').value();
                    }

                }
            },

            'node-item': {

                inherit: true,
                replace: true,
                template: '#node-item',

                computed: {

                    isActive: function() {
                        return this.node === this.current;
                    },

                    isParent: function() {
                        return _(this.nodes).filter({ menu: this.menu.id, parentId: this.node.id }).value().length;
                    }

                },

                methods: {

                    'delete': function(e) {

                        e.preventDefault();
                        e.stopPropagation();

                        this.Nodes.delete({ id: this.node.id }, vm.load);
                    }

                }
            },

            'node-edit': {

                inherit: true,

                data: function() {
                    return { node: {} }
                },

                watch: {

                    current: function() {
                        this.reload();
                    }

                },

                computed: {

                    type: function() {
                        return (_.find(this.types, { id: this.node.type }) || {});
                    }

                },

                methods: {

                    reload: function() {

                        var self = this;

                        if (!this.current.id && !this.current.type) {
                            return;
                        }

                        this.$http.get(this.$url('admin/site/edit', { id: this.current.id, type: this.current.type }), function(data) {

                            if (self.edit) {
                                self.edit.$destroy();
                            }

                            data.node.data = _.isArray(data.node.data) ? {} : data.node.data || {};

                            self.$set('node', data.node);

                            self.edit = self.$addChild({

                                inherit: true,
                                el: self.$$.edit,
                                template: data.view,

                                ready: function() {
                                    UIkit.tab(this.$$.tab, { connect: this.$$.content });
                                }

                            });

                        });
                    },

                    getPath: function() {
                        var parent = _.find(this.nodes, { 'id': this.node.parentId });
                        return (parent ? parent.path : '') + '/' + (this.node.slug || '');
                    },

                    save: function (e) {

                        e.preventDefault();

                        this.Nodes.save({ id: this.node.id }, { node: this.node }, function(node) {
                            vm.current.id = parseInt(node.id);
                            vm.load();
                        });
                    }

                }

            }
        }

    });

});
