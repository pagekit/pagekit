<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>

        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>
            <h2 class="uk-margin-remove">{{ menu.label | trans }}</h2>

            <div class="uk-margin-left" v-show="selected.length">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="uk-icon-trash-o" title="Delete" data-uk-tooltip="{delay: 500}" v-on="click: remove"></a></li>
                    <li><a class="uk-icon-check-circle-o" title="Enable" data-uk-tooltip="{delay: 500}" v-on="click: status(1)"></a></li>
                    <li><a class="uk-icon-ban" title="Disable" data-uk-tooltip="{delay: 500}" v-on="click: status(0)"></a></li>
                </ul>
            </div>
        </div>

        <div class="uk-position-relative" data-uk-margin>
            <div data-uk-dropdown="{ mode: 'click' }">

                <a class="uk-button uk-button-primary" v-on="click: $event.preventDefault()">{{ 'Add Page' | trans }}</a>

                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li v-repeat="type: types"><a v-attr="href: $url('admin/site/edit', { id: type.id, menu: menu.id })">{{ type.label }}</a></li>
                    </ul>
                </div>

            </div>
        </div>

    </div>

    <div class="uk-overflow-container">

        <div class="pk-table-fake pk-table-fake-header pk-table-fake-header-indent-nested">
            <div class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></div>
            <div class="pk-table-min-width-100">{{ 'Title' | trans }}</div>
            <div class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</div>
            <div class="pk-table-width-150">{{ 'URL' | trans }}</div>
        </div>

    </div>

    <ul class="uk-nestable" v-el="nestable">
        <node-item v-repeat="node: tree[0]"></node-item>
    </ul>

</template>

<script>

    module.exports = {

        paramAttributes: ['menu', 'types'],

        created: function () {
            this.Nodes = this.$resource('api/site/node/:id');
        },

        watch: {

            menu: 'load',

            nodes: function() {

                var vm = this;

                // TODO this is still buggy
                UIkit.nestable(vm.$$.nestable, { maxDepth: 20, group: 'site.nodes' }).off('change.uk.nestable').on('change.uk.nestable', function (e, el, type, root, nestable) {
                    if (type !== 'removed') {
                        vm.Nodes.save({ id: 'updateOrder' }, { menu: vm.menu.id, nodes: nestable.list() }, function() {
                            vm.load();

                            UIkit.notify(this.$trans('Order updated.'));
                        });
                    }
                });

            }

        },

        methods: {

            add: function (menu, type) {
                this.select({ menu: menu.id, type: type.id })
            },

            load: function () {
                this.Nodes.query({ menu: this.$get('menu.id') }, function (nodes) {
                    this.$set('nodes', nodes);
                    this.$set('tree', _(nodes).sortBy('priority').groupBy('parentId').value());
                });
            },

            status: function (status) {

                var nodes = this.getSelected();

                nodes.forEach(function (user) {
                    user.status = status;
                });

                this.Nodes.save({ id: 'bulk' }, { nodes: nodes }, function (data) {
                    this.load();
                    UIkit.notify('Page(s) saved.');
                });
            },

            toggleStatus: function (node) {
                node.status = !!node.status ? 0 : 1;

                this.Nodes.save({ id: node.id }, { node: node }, function (data) {
                    this.load();
                    UIkit.notify('Page saved.');
                });
            },

            remove: function() {
                UIkit.modal.confirm(this.$trans('Are you sure?'), function() {

                    this.Nodes.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                        this.load();
                        UIkit.notify('Page(s) deleted.');
                    });

                }.bind(this));
            },

            getSelected: function () {
                var vm = this;

                return this.nodes.filter(function (node) {
                    return vm.selected.indexOf(node.id.toString()) !== -1;
                });
            }

        },

        components: {

            'node-item': require('./node.vue')

        }

    }

</script>
