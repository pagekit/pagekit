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

            return this.$resource('api/site/').query(function(data) {
                this.$set('nodes', data.nodes);
                this.$set('menus', data.menus);

                this.$emit('loaded');

            });

        }

    }

};
