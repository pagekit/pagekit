<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>

        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>
            <h2 class="uk-margin-remove">{{ menu.label | trans }}</h2>

            <div class="uk-margin-left" v-show="selected.length">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="uk-icon-trash-o" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove" v-confirm="'Are you sure?'"></a></li>
                    <li><a class="uk-icon-check-circle-o" title="{{ 'Enable' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(1)"></a></li>
                    <li><a class="uk-icon-ban" title="{{ 'Disable' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(0)"></a></li>
                    <li><a class="uk-icon-home" title="{{ 'Frontpage' | trans }}" data-uk-tooltip="{delay: 500}" v-show="selected.length === 1" v-on="click: setFrontpage()"></a></li>
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
        <node v-repeat="node: tree[0]"></node>
    </ul>

</template>

<script>

    module.exports = {

        props: ['menu'],

        data: function() {
            return _.merge({
                nodes: [],
                tree: [],
                selected: []
            }, window.$data);
        },

        created: function () {
            this.Nodes = this.$resource('api/site/node/:id');

            this.$watch('menu', this.load, {immediate: true})
        },

        watch: {

            nodes: function() {

                var vm = this;

                // TODO this is still buggy
                UIkit.nestable(vm.$$.nestable, { maxDepth: 20, group: 'site.nodes' }).off('change.uk.nestable').on('change.uk.nestable', function (e, el, type, root, nestable) {
                    if (e.target.tagName === 'UL' && type !== 'removed') {
                        vm.Nodes.save({ id: 'updateOrder' }, { menu: vm.menu.id, nodes: nestable.list() }, function() {
                            vm.load();

                            UIkit.notify(this.$trans('Order updated.'));
                        });
                    }
                });

            }

        },

        methods: {

            load: function () {

                this.$set('selected', []);

                this.Nodes.query({ menu: this.$get('menu.id') }, function (nodes) {
                    this.$set('nodes', nodes);
                    this.$set('tree', _(nodes).sortBy('priority').groupBy('parentId').value());
                });
            },

            setFrontpage: function() {

                var frontpage = this.selected[0];

                this.Nodes.save({ id: 'frontpage' }, { id: frontpage }, function () {
                    this.load();
                    this.$set('frontpage', frontpage);
                    UIkit.notify('Frontpage set.');
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
                this.Nodes.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                    this.load();
                    UIkit.notify('Page(s) deleted.');
                });
            },

            getSelected: function () {

                var vm = this;

                return this.nodes.filter(function (node) {
                    return vm.selected.indexOf(node.id.toString()) !== -1;
                });
            }

        },

        components: {

            node: require('./node.vue')

        }

    }

</script>
