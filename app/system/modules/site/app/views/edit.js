var $ = require('jquery');
var _ = require('lodash');
var UIkit = require('uikit');

var Site = Vue.extend({

    data: function () {
        return _.merge({}, window.$data);
    },

    ready: function () {
        this.Nodes = this.$resource('api/site/node/:id');
        this.tab = UIkit.tab(this.$$.tab, { connect: this.$$.content });
    },

    computed: {

        sections: function () {

            var sections = [], type = this.$get('type.id');

            _.each(this.$options.components, function (component) {

                var section = component.options.section;

                if (section && (!section.active || type && type.match(section.active))) {
                    sections.push(component.options.section);
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

            var data = { node: this.node };

            this.$broadcast('save', data);

            this.Nodes.save({ id: this.node.id }, data, function (data) {

                this.$set('node', data.node);

                UIkit.notify(this.$trans('%type% saved.', { type: this.type.label }));

            });
        }

    },

    partials: {

        settings: require('../templates/settings.html')

    },

    components: {

        alias: require('../components/alias.vue')

    }

});

$(function () {

    new Site().$mount('#site-edit');

});

module.exports = Site;
