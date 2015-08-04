module.exports = {

    data: $.extend(true, {
        position: undefined,
        selected: [],
        config: {filter:{search:'', node:''}}
    }, window.$data),

    ready: function () {

        this.$set('indexedNodes', _($data.config.nodes).groupBy('id').value());

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

            var options = [{text: this.$trans('Pages'), value: ''}],
                nodes   = _($data.config.nodes).groupBy('menu').value(),
                opts;

            Object.keys(nodes).forEach(function(menu){

                opts = [];

                nodes[menu].forEach(function(node){
                    opts.push({text:node.title, value:node.id});
                });

                options.push({label: menu, options:opts});
            });

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
        },

        infilter: function(widget) {

            if (this.config.filter.search) {
                return widget.title.toLowerCase().indexOf(this.config.filter.search.toLowerCase()) != -1;
            }

            if (this.config.filter.node && widget.nodes.length) {

                var selected = Number(this.config.filter.node), ret = false;

                return widget.nodes.filter(function(node){
                    return (Number(node) === selected)
                }).length;
            }

            return true;
        },

        emptyafterfilter: function(widgets) {

            widgets = widgets || this.widgets;

            var vm = this;

            return !widgets.filter(function(widget){
                return vm.infilter(widget);
            }).length;
        },

        getSingleNodeTitle: function(widget) {

            return !widget.nodes.length ? 'All': ( widget.nodes.length == 1 ? this.indexedNodes[widget.nodes[0]][0].title:'Selected');
        },

        getNodeTitle: function(id) {

            return this.indexedNodes[id] ? this.indexedNodes[id][0].title : '';
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
