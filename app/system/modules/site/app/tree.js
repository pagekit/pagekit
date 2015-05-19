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
            var parents = _(this.nodes).sortBy('priority').groupBy('parentId').value();
            this.$set('tree', _.merge(parents, _.groupBy(parents[0] || [], 'menu')));
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
