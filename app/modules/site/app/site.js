jQuery(function () {

    var vm = new Vue({

        el: '#js-site',

        data: _.merge({ current: {} }, $data),

        created: function () {

            Vue.validators['unique'] = function(value) {
                var menu = _.find(this.menus, { id: value });
                return !menu || this.menu.oldId == menu.id;
            };

            this.Nodes = this.$resource('api/system/node/:id');
            this.Menus = this.$resource('api/system/menu/:id', {}, { 'update': { method: 'PUT' }});

            Vue.http.defaults.options = _.extend({}, Vue.http.defaults.options, { error: function (msg) {
                UIkit.notify(msg, 'danger');
            }});

            this.load();
        },

        watch: {

            nodes: function() {
                this.select(_.find(this.nodes, { id: this.current.id }) || this.nodes[0]);
            }

        },

        methods: {

            select: function(node) {
                this.$set('current', node || {});
            },

            load: function() {

                this.Nodes.query(function (nodes) {
                    vm.$set('nodes', nodes);
                });

                this.Menus.query(function (menus) {
                    vm.$set('menus', menus);
                });

            }

        },

        components: {

            'menu-list': {

                inherit : true,
                template: '#menu-list',

                methods: {

                    add: function(menu, type) {
                        vm.select({ menu: menu.id, type: type.id })
                    },

                    edit: function (menu) {

                        menu = Vue.util.extend({}, menu || { label: '', id: '' });
                        menu.oldId = menu.id;

                        if (menu.fixed) return;

                        if (!this.modal) {
                            this.modal = UIkit.modal('#modal-menu');
                        }

                        this.modal.show();
                        this.$set('menu', menu);
                    },

                    save: function () {
                        this.Menus[this.menu.oldId ? 'update' : 'save']({ id: this.menu.id }, this.menu, vm.load);
                        this.cancel();
                    },

                    'delete': function () {
                        this.Menus.delete({ id: this.menu.id }, vm.load);

                        this.cancel();
                    },

                    cancel: function (e) {
                        if (e) e.preventDefault();
                        this.$set('menu', null);
                        this.modal.hide();
                    }

                }
            },

            'node-list': {

                inherit: true,
                replace: true,
                template: '#node-list',

                ready: function () {

                    if (this.node) return;

                    var self = this, nestable = UIkit.nestable(this.$el);

                    nestable.element.on('change.uk.nestable', function (e, el, type) {
                        if (type !== 'removed') {
                            vm.Nodes.save({ id: 'updateOrder' }, { menu: self.menu.id, nodes: nestable.list() }, vm.load);
                        }
                    });
                },

                computed: {

                    children: function() {
                        return _(this.nodes).filter({ menu: this.menu.id, parentId: this.node ? this.node.id : 0 }).sortBy('priority').value();
                    }

                },

                methods: {

                    hasChildren: function(node) {
                        return _(this.nodes).filter({ menu: this.menu.id, parentId: node.id }).value().length;
                    },

                    'delete': function(e) {

                        e.preventDefault();
                        e.stopPropagation();

                        this.Nodes.delete({ id: e.targetVM.node.id }, vm.load);
                    }

                }
            },

            'node-edit': {

                inherit: true,
                template: '#node-edit',

                data: function() {
                    return { node: {} }
                },

                watch: {

                    current: function() {
                        this.reset();
                    }

                },

                computed: {

                    type: function() {
                        return (_.find(this.types, { id: this.node.type }) || {}).label;
                    }

                },

                methods: {

                    reset: function() {
                        this.$set('node', _.extend({}, this.current));
                    },

                    getPath: function() {
                        var parent = _.find(this.nodes, { 'id': this.node.parentId });
                        return (parent ? parent.path : '') + '/' + (this.node.slug || '');
                    },

                    save: function (e) {

                        e.preventDefault();

                        this.Nodes.save({ id: this.node.id }, { node: this.node }, vm.load);
                    }

                }

            }
        }

    });

});
