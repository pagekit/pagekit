module.exports = {

    data: $.extend(true, {
        position: undefined,
        selected: []
    }, window.$data),

    ready: function () {
        this.load();
    },

    computed: {

        positions: function () {
            return this.config.positions.concat(this.unassigned);
        },

        unassigned: function () {

            var config = this.config, ids = _.pluck(this.widgets, 'id').filter(function (id) {
                return !config.positions.some(function (position) {
                    return position.assigned.indexOf(id) !== -1;
                });
            });

            return {name: '_unassigned', label: 'Unassigned', assigned: ids};
        }

    },

    methods: {

        active: function (position) {
            return this.position === position || this.position.name == position.name;
        },

        select: function (position) {

            if (position) {
                this.$set('selected', []);
            }

            this.$set('position', position);
        },

        assign: function (position, ids) {
            return this.resource.save({id: 'assign'}, {position: position, ids: ids}, function (data) {
                this.config.$set('positions', data.positions);
                this.$set('selected', []);
            });
        },

        move: function (position, ids) {

            ids = _.map(ids, _.parseInt);
            position = _.find(this.positions, 'name', position);

            this.assign(position.name, position.assigned.concat(ids)).success(function () {
                UIkit.notify(this.$transChoice('{1} %count% Widget moved|]1,Inf[ %count% Widgets moved', ids.length, {count: ids.length}));
            });
        },

        status: function () {
            var widgets = this.getSelected();

            widgets.forEach(function (widget) {
                widget.status = status;
            });

            this.resource.save({id: 'bulk'}, {widgets: widgets}, function () {
                this.load();
                this.$set('selected', []);
                UIkit.notify('Widget(s) saved.');
            });
        },

        toggleStatus: function (widget) {

            widget.status = widget.status ? 0 : 1;

            this.resource.save({id: widget.id}, {widget: widget}, function () {
                UIkit.notify(this.$trans('Widget saved.'));
            });
        },

        getSelected: function () {
            return this.widgets.filter(function (widget) {
                return this.selected.indexOf(widget.id.toString()) !== -1;
            }, this);
        }

    },

    filters: {

        show: function (position) {

            if (!this.position) {
                return position.name != '_unassigned' ? position.assigned.length : 0;
            }

            return this.active(position);
        },

        assigned: function (ids) {

            var widgets = this.widgets;

            return ids.map(function (id) {
                return _.find(widgets, 'id', id);
            }).filter(function (widget) {
                return widget !== undefined;
            });
        }

    },

    components: {

        position: {

            inherit: true,
            replace: false,

            ready: function () {

                var vm = this;

                UIkit.sortable(this.$el, {group: 'position', removeWhitespace: false})
                    .element.off('change.uk.sortable')
                    .on('change.uk.sortable', function (e, sortable, element, action) {
                        if (action == 'added' || action == 'moved') {
                            vm.assign(vm.pos.name, _.pluck(sortable.serialize(), 'id'));
                        }
                    });
            }

        },

        item: {

            inherit: true,
            replace: false,

            computed: {

                type: function () {
                    if (this.widget) {
                        return _.find(this.config.types, {name: this.widget.type});
                    }
                }

            }

        }

    }

};

$(function () {

    (new Widgets(module.exports)).$mount('#widgets');

});
