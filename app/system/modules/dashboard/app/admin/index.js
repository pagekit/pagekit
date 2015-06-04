var $ = require('jquery');
var UIkit = require('uikit');
var Vue = require('vue');

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

        'widget-panel': {

            replace: true,
            inherit: true,

            ready: function() {
                if (this.type.editable !== false) {
                    this.$watch('widget', Vue.util.debounce(this.save, 500), true, false);
                }
            },

            computed: {

                type: function() {
                    return this.getType(this.widget.type);
                },

                component: function() {
                    return this.type.component;

                },

                isEditing: function() {
                    return !!this.editing[this.widget.id];
                }

            },

            methods: {

                edit: function(force) {

                    var id = this.widget.id;

                    if (!force && this.editing[id]) {
                        this.editing.$delete(id);
                    } else {
                        this.editing.$set(id, true);
                    }

                },

                save: function() {

                    var data = { widget: this.widget };

                    this.$broadcast('save', data);

                    this.Widgets.save({ id: this.widget.id }, data);

                },

                remove: function() {

                    var id = this.widget.id;

                    UIkit.modal.confirm(this.$trans('Are you sure?'), function() {

                        this.Widgets.delete({ id: id }, function() {
                            this.widgets.splice(_.findIndex(this.widgets, { id: id }), 1);
                        });

                    }.bind(this));
                }

            }

        },

        feed: require('./widgets/feed.vue'),
        weather: require('./widgets/weather.vue')

    }

});

$(function () {

    new Dashboard().$mount('#dashboard');

});

module.exports = Dashboard;
