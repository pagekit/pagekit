<template>

    <label><input type="checkbox" v-model="all"> {{ 'All Pages' | trans }}</label>

    <ul class="uk-list uk-margin-top-remove" v-for="menu in menus" v-if="menu.count">
        <li class="pk-list-header">{{ menu.label }}</li>
        <partial name="node-partial" v-for="node in grouped[menu.id][0]"></partial>
    </ul>
</template>

<script>

    module.exports = {

        props: {
            trash: {
                type: Boolean,
                default: false
            },
            active: {
                type: Array,
                default: function() {
                    return [];
                }
            }
        },

        data: function () {
            return {
                'menus': [],
                'nodes': [],
                'all': true
            }
        },

        created: function () {
                this.all =  !this.active || !this.active.length;
        },

        watch: {

            active: function (active) {
                this.all =  !active || !active.length;
            },

            all: function (all) {
                if (all) {
                    this.active = [];
                }
            }

        },

        ready: function () {

            var vm = this;

            Vue.Promise.all([
                    this.$http.get('api/site/node'),
                    this.$http.get('api/site/menu')
                ])
                .then(function(responses) {
                    vm.$set('nodes', responses[0].data);
                    vm.$set('menus', vm.trash ? responses[1].data : _.reject(responses[1].data, 'id', 'trash'));
                }, function () {
                    vm.$notify('Could not load config.', 'danger');
                });
        },

        computed: {

            grouped: function() {
                return _(this.nodes).groupBy('menu').mapValues(function(nodes) {
                    return _(nodes || {}).sortBy('priority').groupBy('parent_id').value();
                }).value();
            }

        },

        partials: {

            'node-partial': '<li>' +
                '<label><input type="checkbox" :value="node.id" v-model="active" number> {{ node.title }}</label>' +
                '<ul class="uk-list" v-if="grouped[menu.id][node.id]">' +
                    '<partial name="node-partial" v-for="node in grouped[menu.id][node.id]"></partial>' +
                '</ul>' +
            '<li>'

        }
    };

    window.Vue.component('input-tree', function (resolve) {
        resolve(module.exports);
    });

</script>
