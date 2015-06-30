module.exports = {

    data: $.extend(true, {
        position: undefined,
        selected: []
    }, window.$data),

    ready: function () {
        this.load();
    },

    computed: {

        positions: function() {
            return this.config.positions.concat(
                {name:'', label: this.$trans('Inactive'), description: '', assigned: this.unassigned}
            );
        },

        unassigned: function() {
            return _.pluck(this.widgets, 'id').filter(function(id) {
                return !this.config.positions.some(function(position) {
                    return position.assigned.indexOf(id) !== -1;
                });
            }.bind(this));
        }

    },

    methods: {

        active: function (position) {

            if (!position) {
                return this.position === position;
            }

            return this.position && this.position.name === position.name;
        },

        select: function (position) {
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
            this.assign(position.name, _.unique(position.assigned.concat(_.map(ids, _.parseInt))));
        }

    },

    filters: {

        show: function (position) {
            return !this.position || this.position.name === position.name;
        },

        assigned: function (ids) {
            return ids.map(function (id) {
                return _.find(this.widgets, 'id', id);
            }.bind(this)).filter(function (widget) {
                return widget !== undefined;
            }.bind(this));
        }

    },

    components: {

        'v-position': {

            inherit: true,
            replace: false,

            ready: function () {

                var vm = this;
                UIkit.sortable(this.$el, {group: 'position', removeWhitespace: false})
                    .element.off('change.uk.sortable')
                    .on('change.uk.sortable', function (e, sortable, element, action) {
                        if (action == 'added' || action == 'moved') {
                            vm.assign(vm.p.name, _.pluck(sortable.serialize(), 'id'));
                        }
                    });
            }

        },

        'v-item': {

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
