var $ = require('jquery');
var _ = require('lodash');

module.exports = {

    created: function () {

        this.Nodes = this.$resource('api/site/node/:id');
        this.Menus = this.$resource('api/site/menu/:id', {}, { 'update': { method: 'PUT' }});

        this.$add('nodes', []);
        this.$add('menus', []);
        this.$add('tree', {});

        this.load();

    },

    events: {

        loaded: function() {

            var vm = this,
                tree = _(this.nodes).sortBy('priority').groupBy('parentId').value(),
                menus = _.groupBy(tree[0] || [], 'menu');

            _.forEach(menus, function(nodes, menu) {
                if (!_.find(vm.menus, {id: menu})) {
                    menus[''] = menus[''].concat(nodes);
                    delete menus[menu];
                }
            });

            this.$set('tree', _.merge(tree, menus));
        }

    },

    methods: {

        load: function () {

            var d1 = $.Deferred(), d2 = $.Deferred(), deferred = $.when(d1, d2);

            deferred.done(function(nodes, menus) {

                this.$set('nodes', nodes);
                this.$set('menus', menus);

                this.$emit('loaded');

            }.bind(this));

            this.Nodes.query(function (nodes) {
                d1.resolve(nodes);
            });

            this.Menus.query(function (menus) {
                d2.resolve(menus);
            });

            return deferred;
        }

    }

};
