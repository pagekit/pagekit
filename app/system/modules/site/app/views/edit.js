window.Site = module.exports = {

    data: function () {
        return _.merge({}, window.$data);
    },

    ready: function () {
        this.Nodes = this.$resource('api/site/node/:id');
        this.tab = UIkit.tab(this.$$.tab, {connect: this.$$.content});
    },

    computed: {

        sections: function () {

            var type = this.$get('type.id'), sections = [];

            _.forIn(this.$options.components, function (component, name) {

                var options = component.options || {}, section = options.section;

                if (section && (!section.active || type && type.match(section.active))) {
                    section.name = name;
                    sections.push(section);
                }

            });

            return sections;
        },

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
                    window.history.replaceState({}, '', this.$url('admin/site/page/edit', {id: data.node.id}));
                }

                this.$set('node', data.node);

                UIkit.notify(this.$trans('%type% saved.', {type: this.type.label}));

            }, function (data) {

                UIkit.notify(data, 'danger');
            });
        }

    },

    partials: {

        settings: require('../templates/settings.html')

    },

    components: {

        link: require('../components/link.vue'),
        appearance: require('../components/site-appearance.vue')

    }

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#site-edit');

});
