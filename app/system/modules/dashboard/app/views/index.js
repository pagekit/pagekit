var Dashboard = Vue.extend({

    data: function() {
        return window.$data;
    },

    created: function() {

        this.Widgets = this.$resource('admin/dashboard/:id');

        var self = this;
        this.$set('widgets', this.widgets.filter(function(widget) {
            return self.getType(widget.type);
        }));

        this.$set('editing', {});
    },

    computed: {

        columns: function() {
            var i = 0;
            return _.groupBy(this.widgets, function() {
                return i++ % 3;
            });
        }

    },

    methods: {

        add: function(type) {

            this.Widgets.save({ widget: _.merge({ type: type.id }, type.defaults)}, function(data) {
                this.widgets.push(data);
                this.editing.$set(data.id, true);
            });

        },

        getType: function(id) {
            return _.find(this.getTypes(), { id: id });

        },

        getTypes: function() {

            return _(this.$options.components)
                .filter(function(component) { return _.has(component, 'options.type') })
                .map(function(component) { return _.merge( component.options.type, { component: component.options.name } ) })
                .value();
        }

    },

    components: {

        'widget-panel': require('../components/widget-panel'),
        feed: require('../components/widget-feed.vue'),
        location: require('../components/widget-location.vue')

    }

});

$(function () {

    new Dashboard().$mount('#dashboard');

});

module.exports = Dashboard;
