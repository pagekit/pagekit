module.exports = window.Widgets = Vue.extend({

    data: function () {
        return {
            widgets: [],
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
        }

    },

    partials: {

        settings: require('./templates/widget-settings.html')

    },

    components: {

        appearance: require('./components/widget-appearance.vue'),
        assignment: require('./components/widget-assignment.vue')

    }

});
