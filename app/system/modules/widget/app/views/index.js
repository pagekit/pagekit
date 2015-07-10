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
            return this.theme.positions.concat(this.unassigned);
        },

        unassigned: function () {
            return {name: '_unassigned', label: 'Unassigned', assigned: _.pluck(this.get('unassigned'), 'id')};
        },

        empty: function () {
            return !this.position && !this.get('assigned').length;
        }

    },

    methods: {

        get: function (filter) {

            var filters = {

                selected: function (widget) {
                    return this.selected.indexOf(widget.id.toString()) !== -1;
                },

                assigned: function (widget) {
                    return this.theme.positions.some(function (position) {
                        return position.assigned.indexOf(widget.id) !== -1;
                    });
                },

                unassigned: function (widget) {
                    return !this.theme.positions.some(function (position) {
                        return position.assigned.indexOf(widget.id) !== -1;
                    });
                }

            };

            return filters[filter] ? this.widgets.filter(filters[filter], this) : this.widgets;
        },

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
                this.$set('theme', data.theme);
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

            var widgets = this.get('selected');

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
        }

    },

    filters: {

        show: function (position) {

            if (!this.position) {
                return position.widgets ? position.widgets.length : 0;
            }

            return this.active(position);
        },

        assigned: function (ids) {
            return this.get('assigned').filter(function (widget) {
                return ids.indexOf(widget.id) !== -1;
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
                        return _.find(this.types, {name: this.widget.type});
                    }
                }

            }

        }

    }

};

$(function () {

    (new Widgets(module.exports)).$mount('#widgets');

});
