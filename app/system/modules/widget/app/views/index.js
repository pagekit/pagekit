module.exports = {

    data: $.extend(true, {
        position: undefined,
        selected: [],
        config: {filter: {search: '', node: ''}}
    }, window.$data),

    ready: function () {

        UIkit.init();
        this.load();

    },

    computed: {

        positions: function () {
            return this.config.positions.concat(this.unassigned);
        },

        unassigned: function () {

            var widgets = this.get('unassigned');

            return {name: '_unassigned', label: 'Unassigned', assigned: _.pluck(widgets, 'id'), widgets: widgets};
        },

        empty: function () {
            return !this.position && !this.get('assigned').length;
        },

        nodes: function () {

            var options = [],
                nodes = _(this.config.nodes).groupBy('menu').value();

            _.forEach(this.config.menus, function (menu, name) {

                var opts = nodes[name];

                if (!opts) {
                    return;
                }

                options.push({
                    label: menu.label, options: _.map(opts, function (node) {
                        return {text: node.title, value: node.id};
                    })
                });

            }, this);

            return options;
        }

    },

    methods: {

        get: function (filter) {

            var filters = {

                selected: function (widget) {
                    return this.selected.indexOf(widget.id.toString()) !== -1;
                },

                assigned: function (widget) {
                    return this.config.positions.some(function (position) {
                        return position.assigned.indexOf(widget.id) !== -1;
                    });
                },

                unassigned: function (widget) {
                    return !this.config.positions.some(function (position) {
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
                this.$set('config.positions', data.positions);
                this.$set('selected', []);
            });
        },

        move: function (position, ids) {

            position = _.find(this.positions, 'name', position);

            this.assign(position.name, position.assigned.concat(ids)).success(function () {
                this.$notify(this.$transChoice('{1} %count% Widget moved|]1,Inf[ %count% Widgets moved', ids.length, {count: ids.length}));
            });
        },

        copy: function () {

            var widgets = _.merge([], this.widgets.filter(function (widget) {
                return -1 !== this.selected.indexOf(widget.id);
            }, this));

            widgets.forEach(function (widget) {
                delete widget.id;
            });

            this.resource.save({id: 'bulk'}, {widgets: widgets}, function (data) {
                this.load();
                this.$set('config.positions', data.positions);
                this.$set('selected', []);
                this.$notify('Widget(s) copied.');
            });
        },

        remove: function () {
            this.resource.delete({id: 'bulk'}, {ids: this.selected}, function() {
                this.load();
                this.$set('selected', []);
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
                this.$notify('Widget(s) saved.');
            });
        },

        toggleStatus: function (widget) {

            widget.status = widget.status ? 0 : 1;

            this.resource.save({id: widget.id}, {widget: widget}, function () {
                this.load();
                this.$notify('Widget saved.');
            });
        },

        infilter: function (widget) {

            if (this.config.filter.search) {
                return widget.title.toLowerCase().indexOf(this.config.filter.search.toLowerCase()) != -1;
            }

            if (this.config.filter.node && widget.nodes.length) {
                return widget.nodes.some(function (node) {
                    return node === this.config.filter.node;
                }, this);
            }

            return true;
        },

        emptyafterfilter: function (widgets) {

            widgets = widgets || this.widgets;

            return !widgets.some(function (widget) {
                return this.infilter(widget);
            }, this);
        },

        getPageFilter: function (widget) {

            if (!widget.nodes.length) {
                return this.$trans('All');
            } else if (widget.nodes.length > 1) {
                return this.$trans('Selected');
            } else {
                return (_.find(this.config.nodes, 'id', widget.nodes[0]) || {}).title;
            }

        },

        isSelected: function (id) {
            return this.selected.indexOf(id) !== -1;
        }

    },

    filters: {

        show: function (position) {

            if (!this.position) {
                return (position.name != '_unassigned' ? position.widgets.length : 0);
            }

            return this.active(position);
        },

        type: function (widget) {
            return _.find(this.types, {name: widget.type});
        },

        assigned: function (ids) {
            return ids.map(function (id) {
                return _.find(this.widgets, 'id', id);
            }, this).filter(function (widget) {
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

                // disable sorting on unassigned position
                if (this.$el.getAttribute('data-position') == '_unassigned') {
                    return;
                }

                UIkit.sortable(this.$el, {group: 'position', removeWhitespace: false})
                    .element.off('change.uk.sortable')
                    .on('change.uk.sortable', function (e, sortable, element, action) {
                        if (action == 'added' || action == 'moved') {
                            vm.assign(vm.pos.name, _.pluck(sortable.serialize(), 'id'));
                        }
                    });
            }

        }

    },

    mixins: [window.Widgets]

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#widgets');

});
