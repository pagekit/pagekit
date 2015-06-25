module.exports = {

    data: $.extend(true, {
        selected: [],
        config: {filter: {}}
    }, window.$data),

    ready: function () {

        var vm = this;

        this.load();

        $(this.$el).on('change.uk.sortable', function (e, sortable, el, mode) {

            el = $(el);

            // if (mode == 'moved' || mode == 'added') {

            //     var newpos   = el.parent().data('position'),
            //         newindex = el.index(),
            //         oldpos   = el.data('start-list').data('position'),
            //         oldindex = el.data('start-index');

            //     vm.positions[oldpos].widgets[oldindex].position = newpos;
            //     vm.positions[newpos].widgets.splice(newindex, 0, vm.positions[oldpos].widgets.splice(oldindex, 1)[0]);
            // }

        });
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

    watch: {

        positions: function () {
            UIkit.init(this.$el);
        }

    },

    methods: {

        hasWidgets: function (position) {
            return this.positions[position.id] !== undefined;
        }

    },

    components: {

        'v-item': {

            inherit: true,

            props: ['id'],

            computed: {

                widget: function () {
                    return this.widgets[this.id] || {};
                },

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

    (new Widgets(module.exports)).$mount('#widget-index');

});
