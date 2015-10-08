window.Site = module.exports = {

    data: function () {
        return _.merge({}, window.$data);
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
        this.Nodes = this.$resource('api/site/node/:id');
        this.tab = UIkit.tab(this.$$.tab, {connect: this.$$.content});
    },

    computed: {

        path: function () {
            return (this.node.path ? this.node.path.split('/').slice(0, -1).join('/') : '') + '/' + (this.node.slug || '');
        }

    },

    methods: {

        save: function (e) {
            e.preventDefault();

            var data = {node: this.node};

            this.$broadcast('save', data);

            this.Nodes.save({id: this.node.id}, data, function (data) {

                if (!this.node.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/site/page/edit', {id: data.node.id}));
                }

                this.$set('node', data.node);

                this.$notify(this.$trans('%type% saved.', {type: this.type.label}));

            }, function (data) {

                this.$notify(data, 'danger');
            });
        }

    },

    partials: {

        'settings': require('../templates/settings.html')

    },

    components: {

        'settings': require('../components/node-settings.vue'),
        'link:settings': require('../components/node-link.vue')

    }

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#site-edit');

});
