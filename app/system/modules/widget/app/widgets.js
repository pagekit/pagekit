module.exports = window.Widgets = Vue.extend({

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

                var indexedwidgets = _(data).groupBy('id').value(),
                    positions      = {};

                $data.config.positions.forEach(function(position) {

                    position.widgets = [];

                    position.assigned.forEach(function(widgetId) {

                        if (indexedwidgets[widgetId]) {
                            position.widgets.push(indexedwidgets[widgetId][0]);
                        }
                    });

                    positions[position.name] = position;
                });

                this.$set('positions', positions);
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
        },

        reassign: function(widget, position, index) {

            this.positions[widget.position].widgets.push(this.positions[position].widgets.splice(index, 1)[0]);
        }

    },

    partials: {

        'settings': require('./templates/widget-settings.html')

    },

    components: {

        'assignment': require('./components/widget-assignment.vue'),
        'widget-text': require('./components/widget-text.vue')

    }

});
