var Vue = require('vue');

module.exports = Vue.extend({

    data: function () {
        return {
            widgets: [],
            positions: {},
            configs: {}
        };
    },

    created: function () {
        this.resource = this.$resource('api/widget/:id');
    },

    methods: {

        load: function () {

            this.resource.query(function (data) {
                this.$set('widgets', data);
                this.$set('positions', _(data).groupBy('position').value());
            });

            this.resource.query({id: 'config'}, function (data) {
                this.$set('configs', data);
            });

        },

        copy: function () {

            var widgets = _.merge([], this.getSelected());

            widgets.forEach(function (widget) {
                delete widget.id;
            });

            this.resource.save({id: 'bulk'}, {widgets: widgets}, this.load);
        },

        remove: function () {
            this.resource.delete({id: 'bulk'}, {ids: this.selected}, this.load);
        },

        reorder: function (position, widgets) {
            this.resource.save({id: 'positions'}, {position: position, widgets: _.pluck(widgets, 'id')}, this.load);
        }

    },

    partials: {

        'settings': require('./widget-settings.html')

    },

    components: {

        'site-text': require('../widgets/text.vue')

    }

});
