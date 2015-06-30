module.exports = {

    data: $.extend(true, {
        position: undefined,
        selected: []
    }, window.$data),

    ready: function () {
        this.load();
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
            position = this.getPosition(position);
            Array.prototype.push.apply(position.assigned, ids);
            this.assign(position.name, position.assigned);
        },

        getPosition: function (position) {
            return _.find(this.config.positions, 'name', position);
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
                UIkit.sortable(this.$el, {group: 'position', removeWhitespace: false})
                    .element.off('change.uk.sortable')
                    .on('change.uk.sortable', function (e, sortable, element, action) {
                        if (action == 'added' || action == 'moved') {
                            var position = vm.getPosition(vm.p.name);
                            position.assigned = _.pluck(sortable.serialize(), 'id');
                            vm.assign(position.name, position.assigned);
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
