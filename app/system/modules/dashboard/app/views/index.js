var Dashboard = Vue.extend({

    data: function() {
        return _.extend({ editing: false }, window.$data);
    },

    created: function() {

        var self = this;

        this.Widgets = this.$resource('admin/dashboard/:id');

        this.$set('widgets', this.widgets.filter(function(widget, idx) {

            if (self.getType(widget.type)) {

                widget.idx    = widget.idx === undefined ? idx:widget.idx;
                widget.column = widget.column === undefined ? 0 : widget.column;

                return true;
            }

            return false;
        }));

        this.$set('editing', {});
    },

    ready: function() {

        var self = this;

        // widget re-ordering
        $(this.$el).find('.uk-sortable[data-column]').each(function(){

            UIkit.sortable(this,{group:'widgets', dragCustomClass: 'pk-sortable-dragged-panel'});

        }).on('change.uk.sortable', function(e, sortable, item, mode) {

            if (!mode) return;

            item     = $(item)
            sortable = sortable.element ? sortable : sortable.data('sortable');

            switch(mode) {
                case 'added':
                case 'moved':

                    var widgets = JSON.parse(JSON.stringify(self.widgets)), column = parseInt(sortable.element.data('column'), 10), data = {}, widget;

                    sortable.element.children().each(function(idx){

                        widget = _.find(widgets, {id: this.getAttribute('data-id')});
                        widget.column = column;
                        widget.idx = idx;
                    });

                    widgets.forEach(function(widget){
                        data[widget.id] = widget;
                    });

                    self.$http.post('admin/dashboard/savewidgets', {widgets: data}, function() {
                        UIkit.notify(this.$trans('Dashboard updated'));
                    });
            }
        });

    },

    filters: {

        column: function (widgets, column) {

            column = parseInt(column || 0, 10);

            return _.sortBy(widgets.filter(function (widget) {
                return widget.column == column;
            }), 'idx');
        }
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

            var column = 0, sortables = $('#dashboard').find('.uk-sortable[data-column]');

            sortables.each(function(idx) {
                column = (this.children.length < sortables.eq(column)[0].children.length) ? idx : column;
            });

            this.Widgets.save({ widget: _.merge({ type: type.id, column:column, idx: 100 }, type.defaults)}, function(data) {
                this.widgets.push(data);
                this.editing.$set(data.id, true);
            });
        },

        getType: function(id) {
            return _.find(this.getTypes(), { id: id });
        },

        getTypes: function() {
            return _(this.$options.components.__proto__)
                .filter(function(component) { return _.has(component, 'options.type') })
                .map(function(component) { return _.merge( component.options.type, { component: component.options.name } ) })
                .value();
        }

    },

    components: {

        panel: require('../components/widget-panel.vue'),
        feed: require('../components/widget-feed.vue'),
        location: require('../components/widget-location.vue')

    }

});

$(function () {

    new Dashboard().$mount('#dashboard');

});

module.exports = Dashboard;
