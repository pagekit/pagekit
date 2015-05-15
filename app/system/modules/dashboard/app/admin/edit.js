var $ = require('jquery');

var Dashboard = Vue.extend({

    data: function() {
        return window.$data;
    },

    created: function () {
        this.Widgets = this.$resource('admin/dashboard/:id');
    },

    filters: {

        active: function(sections) {

            var type = this.$get('type.id');

            return sections.filter(function(section) {
                return !section.active || type && type.match(section.active);
            });
        }

    },

    computed: {

        sections: function () {

            var sections = [];

            _.each(this.$options.components, function (component) {
                if (component.options.section) {
                    sections.push(component.options.section);
                }
            });

            return sections;
        }

    },

    methods: {

        save: function (e) {

            e.preventDefault();

            var data = { widget: this.widget };

            this.$broadcast('save', data);

            this.Widgets.save({ id: this.widget.id }, data, function(data) {
                this.$set('widget', data);
            });

        }

    },

    components: {

        'feed': require('./widgets/feed.vue'),
        'weather': require('./widgets/weather.vue')

    }

});

$(function () {

    new Dashboard().$mount('#widget-edit');

});

module.exports = Dashboard;
