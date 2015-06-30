module.exports = {

    data: $.extend(true, {
        position: undefined,
        selected: []
    }, window.$data),

    ready: function () {
        this.load();
        UIkit.init(this.$el);
    },

    computed: {

        count: function () {
            return this.widgets.length || '';
        },

        positionOptions: function () {
            return [{text: this.$trans('- Assign -'), value: ''}].concat(
                _.map(this.config.positions, function (position) {
                    return {text: this.$trans(position.label), value: position.name};
                }.bind(this))
            );
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
            position = _.find(this.config.positions, 'name', position);
            Array.prototype.push.apply(position.assigned, ids);
            this.assign(position.name, position.assigned);
        }

    },

    filters: {

        show: function (position) {
            return !this.position || this.position.name === position.name;
        },

        exists: function (ids) {
            return ids.filter(function (id) {
                return this.widgets[id] !== undefined;
            }.bind(this));
        }

    },

    components: {

        'v-position': {
            inherit: true,
            replace: false,

            ready: function () {

                var vm = this;
                $(this.$el).on('change.uk.sortable', function (e, sortable, element, action) {
                    if (action == 'added' || action == 'moved') {
                        vm.assign(vm.p.name, _.pluck(sortable.serialize(), 'id'));
                    }
                });
            }

        },

        'v-item': {

            inherit: true,
            replace: false,

            props: ['widget'],

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
