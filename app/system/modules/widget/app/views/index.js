module.exports = {

    data: $.extend(true, {
        selected: [],
        config: {filter: {}}
    }, window.$data),

    ready: function () {

        var vm = this;

        this.load();

        $(this.$el).on('change.uk.sortable', function (e, sortable, ele, mode) {

            if (!mode) return;

            ele = $(ele);

            var newpos   = ele.parent().data('position'),
                newindex = ele.index(),
                oldpos   = ele.data('start-list').data('position'),
                oldindex = ele.data('start-index')

            switch(mode) {

                case 'moved':
                case 'added':

                    vm.positions[oldpos].widgets[oldindex].position = newpos;
                    vm.positions[newpos].widgets.splice(newindex, 0, vm.positions[oldpos].widgets.splice(oldindex, 1)[0]);

                    break;
            }

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
        positions: function() {
            UIkit.init(this.$el);
        }
    },

    methods: {

        getType: function (widget) {
            return _.find(this.config.types, {name: widget.type});
        },

        hasWidgets: function (position) {
            return this.positions[position.id] !== undefined;
        }

    }

};

$(function () {

    (new Widgets(module.exports)).$mount('#widget-index');

});
