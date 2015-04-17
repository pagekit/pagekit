/*global $data*/

jQuery(function ($) {

    Vue.validators['unique'] = function(value) {
        var menu = _.find(this.menus, { id: value });
        return !menu || this.menu.oldId == menu.id;
    };

    Vue.http.options = _.extend({}, Vue.http.options, { error: function (msg) {
        UIkit.notify(msg, 'danger');
    }});

    var vm = new Vue({

        el: '#js-site',

        mixins: [Vue.mixins['site-tree']],

        data: _.merge({ selected: null }, $data),

        events: {

            loaded: 'select'

        },

        methods: {

            select: function(node) {
                if (!node) {
                    node = this.selected && _.find(this.nodes, { id: vm.selected.id }) || this.selectFirst();
                }

                this.$set('selected', node);
            },

            selectFirst: function() {
                var first = null;
                this.menus.some(function (menu) {
                    return first = vm.tree[menu.id][0];
                });

                return first ? first.node : undefined;
            }

        },

        components: {

            'menu-list': {

                inherit : true,

                data: function() {
                    return { menu: null, unmounted: [] };
                },

                methods: {

                    add: function(menu, type) {
                        vm.select({ menu: menu.id, type: type.id })
                    },

                    edit: function (menu) {

                        menu = Vue.util.extend({}, menu || { label: '', id: '' });
                        menu.oldId = menu.id;

                        if (menu.fixed) return;

                        this.$set('menu', menu);

                        this.modal = UIkit.modal(this.$$.modal);
                        this.modal.show();
                    },

                    save: function (e) {
                        if (e) e.preventDefault();
                        this.Menus[this.menu.oldId ? 'update' : 'save']({ id: this.menu.id }, this.menu, vm.load);
                        this.cancel();
                    },

                    'delete': function (e) {
                        if (e) e.preventDefault();
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

            'type-dropdown': {

                inherit: true,

                filters: {

                    unmounted: function(types) {

                        return types.filter(function(type) {
                            return !type.controllers || !_.some(vm.nodes, { type: type.id });
                        })

                    }

                }

            },

            'node-list': {

                inherit: true,
                template: '<node-item v-repeat="item: tree[menu.id]"></node-item>',

                ready: function () {
                    var self = this;
                    UIkit.nestable(this.$el, { maxDepth: 20, group: 'site.nodes' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
                        if (type !== 'removed') {
                            vm.Nodes.save({ id: 'updateOrder' }, { menu: self.menu.id, nodes: nestable.list() }, vm.load);
                        }
                    });
                }
            },

            'node-item': {

                inherit: true,
                replace: true,
                template: '#node-item',

                computed: {

                    node: function() {
                        return this.item.node;
                    },

                    isActive: function() {
                        return this.node === this.selected;
                    },

                    isParent: function() {
                        return this.item.children.length;
                    },

                    isFrontpage: function() {
                        return this.node.id === this.frontpage;
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

                    selected: 'reload'

                },

                computed: {

                    type: function() {
                        return (_.find(this.types, { id: this.node.type }) || {});
                    },

                    path: function() {
                        return (this.node.path ? this.node.path.split('/').slice(0, -1).join('/') : '') + '/' + (this.node.slug || '');
                    },

                    isFrontpage: function() {
                        return this.node.id === this.frontpage;
                    }

                },

                methods: {

                    reload: function() {

                        var self = this;

                        if (!this.selected) {
                            return;
                        }

                        this.$http.get(this.$url('admin/site/edit', (this.selected.id ? { id: this.selected.id } : { type: this.selected.type })), function(data) {

                            if (self.edit) {
                                self.edit.$destroy();
                            }

                            data.node.menu = self.selected.menu;

                            self.$set('node', data.node);

                            $(self.$$.edit).empty().html(data.view);

                            self.edit = self.$addChild({

                                inherit: true,
                                data: data.data,
                                el: self.$$.edit,

                                ready: function() {
                                    UIkit.tab(this.$$.tab, { connect: this.$$.content });
                                }

                            });
                        });
                    },

                    save: function (e) {

                        e.preventDefault();

                        var data = _.merge($(":input", e.target).serialize().parse(), { node: this.node });

                        this.$broadcast('save', data);

                        this.Nodes.save({ id: this.node.id }, data, function(node) {

                            vm.selected.id = parseInt(node.id);
                            vm.load();

                            if (data.frontpage) {
                                vm.$set('frontpage', node.id);
                            }
                        });
                    },

                    cancel: function() {
                        if (this.node.id) {
                            this.reload();
                        } else {
                            this.select();
                        }
                    }

                }

            }
        }

    });

});
