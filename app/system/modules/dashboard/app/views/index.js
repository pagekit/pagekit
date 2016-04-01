var Version = require('../../../../../installer/app/lib/version');

window.Dashboard = {

    el: '#dashboard',

    data: function () {
        return _.extend({
            editing: {},
            update: {}
        }, window.$data);
    },

    created: function () {

        var self = this;

        this.Widgets = this.$resource('admin/dashboard{/id}');

        this.$set('widgets', this.widgets.filter(function (widget, idx) {

            if (self.getType(widget.type)) {

                widget.idx = widget.idx === undefined ? idx : widget.idx;
                widget.column = widget.column === undefined ? 0 : widget.column;

                return true;
            }

            return false;
        }));

        this.checkVersion();
    },

    ready: function () {

        var self = this;

        // widget re-ordering
        var sortables = $(this.$el).find('.uk-sortable[data-column]').each(function () {

            UIkit.sortable(this, {group: 'widgets', dragCustomClass: 'pk-sortable-dragged-panel', handleClass: 'pk-icon-handle'});

        }).on('change.uk.sortable', function (e, sortable, item, mode) {

            if (!mode) {
                return;
            }

            sortable = sortable.element ? sortable : sortable.data('sortable');

            switch (mode) {
                case 'added':
                case 'moved':

                    var widgets = self.widgets, column = parseInt(sortable.element.data('column'), 10), data = {}, widget;

                    sortable.element.children('[data-idx]').each(function (idx) {

                        widget = _.find(widgets, 'id', this.getAttribute('data-id'));
                        widget.column = column;
                        widget.idx = idx;
                    });

                    widgets.forEach(function (widget) {
                        data[widget.id] = widget;
                    });

                    self.$http.post('admin/dashboard/savewidgets', {widgets: data}).then(function () {

                        // cleanup empty items - maybe fixed with future vue.js version
                        sortables.children().each(function () {
                            if (!this.children.length) $(this).remove();
                        });
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

        columns: function () {
            var i = 0;
            return _.groupBy(this.widgets, function () {
                return i++ % 3;
            });
        },

        hasUpdate: function () {
            return this.update && Version.compare(this.update.version, this.version, '>');
        }

    },

    methods: {

        add: function (type) {

            var column = 0, sortables = $('#dashboard').find('.uk-sortable[data-column]');

            sortables.each(function (idx) {
                column = (this.children.length < sortables.eq(column)[0].children.length) ? idx : column;
            });

            this.Widgets.save({widget: _.merge({type: type.id, column: column, idx: 100}, type.defaults)}).then(function (res) {
                var data = res.data;
                this.widgets.push(data);
                this.editing[data.id] = true;
            });
        },

        save: function (widget) {

            var data = {widget: widget};

            this.$broadcast('save', data);
            this.Widgets.save({id: widget.id}, data);
        },

        remove: function (widget) {

            this.Widgets.delete({id: widget.id}).then(function () {
                this.widgets.splice(_.findIndex(this.widgets, {id: widget.id}), 1);
            });
        },

        getType: function (id) {
            return _.find(this.getTypes(), 'id', id);
        },

        getTypes: function () {

            var types = [];

            _.forIn(this.$options.components, function (component, name) {

                var options = component.options || {}, type = options.type;

                if (type) {
                    type.component = name;
                    types.push(type);
                }

            });

            return types;
        },

        checkVersion: function () {

            this.$http.get(this.api + '/api/update', {}, {cache: 60}).then(function (res) {
                var update = res.data[this.channel == 'nightly' ? 'nightly' : 'latest'];

                if (update) {
                    this.$set('update', update);
                }
            });

        }

    },

    components: {

        panel: require('../components/widget-panel.vue'),
        feed: require('../components/widget-feed.vue'),
        location: require('../components/widget-location.vue')

    }

};

Vue.ready(window.Dashboard);
