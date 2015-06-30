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
            this.resource.save({id: 'assign'}, {position: position, ids: ids}, function (data) {
                this.config.$set('positions', data.positions);
                this.$set('selected', []);
            });
        },

        move: function (position, ids) {

            position = _.find(this.positions, 'name', position);
            ids = _.unique(position.assigned.concat(_.map(ids, _.parseInt)));

            this.assign(position.name, ids);
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

        'position': {

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

        'item': {

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
