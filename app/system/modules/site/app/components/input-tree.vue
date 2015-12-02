<template>
    <ul class="uk-list uk-margin-top-remove" v-for="menu in menus" v-if="menu.count">
        <li class="pk-list-header">{{ menu.label }}</li>
        <partial name="node-partial" v-for="node in grouped[menu.id][0]"></partial>
    </ul>
</template>

<script>

    var Promise = require('promise');

    module.exports = {

        props: ['nodes'],

        data: function () {
            return {
                'menus': [],
                'allnodes': []
            }
        },

        activate: function (done) {

            var vm = this;

            Promise
                .all([
                    this.$http.get('api/site/node'),
                    this.$http.get('api/site/menu')
                ])
                .then(function(responses) {
                    vm.$set('allnodes', responses[0].data);
                    vm.$set('menus', responses[1].data);
                    done();
                })
                .catch(function () {
                    vm.$notify('Could not load config.', 'danger');
                });
        },

        computed: {

            grouped: function() {
                return _(this.allnodes).groupBy('menu').mapValues(function(nodes) {
                    return _(nodes || {}).sortBy('priority').groupBy('parent_id').value();
                }).value();

            }
        },

        partials: {

            'node-partial': '<li>' +
                '<label><input type="checkbox" :value="node.id" v-model="nodes" number> {{ node.title }}</label>' +
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
