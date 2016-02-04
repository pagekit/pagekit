window.Site = {

    el: '#site-edit',

    data: function () {
        return _.merge({sections: [], form: {}, active: 0}, window.$data);
    },

    created: function () {

        var sections = [], type = _.kebabCase(this.type.id), active;

        _.forIn(this.$options.components, function (component, name) {

            var options = component.options || {};

            if (options.section) {
                sections.push(_.extend({name: name, priority: 0}, options.section));
            }

        });

        sections = _.sortBy(sections.filter(function (section) {

            active = section.name.match('(.+):(.+)');

            if (active === null) {
                return !_.find(sections, {name: type + ':' + section.name});
            }

            return active[1] == type;
        }, this), 'priority');

        this.$set('sections', sections);

    },

    ready: function () {

        var vm = this;

        this.Nodes = this.$resource('api/site/node{/id}');
        this.tab = UIkit.tab(this.$els.tab, {connect: this.$els.content});

        this.tab.on('change.uk.tab', function (tab, current) {
            vm.active = current.index();
        });

        this.$watch('active', function (active) {
            this.tab.switcher.show(active);
        });

        this.$state('active');

    },

    computed: {

        path: function () {
            return (this.node.path ? this.node.path.split('/').slice(0, -1).join('/') : '') + '/' + (this.node.slug || '');
        }

    },

    methods: {

        save: function () {
            var data = {node: this.node};

            this.$broadcast('save', data);

            this.Nodes.save({id: this.node.id}, data).then(function (res) {
                    var data = res.data;
                    if (!this.node.id) {
                        window.history.replaceState({}, '', this.$url.route('admin/site/page/edit', {id: data.node.id}));
                    }

                    this.$set('node', data.node);

                    this.$notify(this.$trans('%type% saved.', {type: this.type.label}));

                }, function (res) {
                    this.$notify(res.data, 'danger');
                }
            );
        }

    },

    partials: {

        settings: require('../templates/settings.html')

    },

    components: {

        'settings': require('../components/node-settings.vue'),
        'link:settings': require('../components/node-link.vue')

    }

};

Vue.ready(window.Site);
