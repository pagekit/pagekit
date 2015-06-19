module.exports = {

    data: $.extend(true, {
        selected: [],
        config: {filter: {}}
    }, window.$data),

    ready: function () {
        this.load();
    },

    watch: {

        widgets: function () {

            $('.uk-nestable', this.$el).each(function () {
                UIkit.nestable(this, {maxDepth: 0, group: 'positions'}).off('change.uk.nestable').on('change.uk.nestable', function (e, el, type, root, nestable) {
                    // update postions ...
                });
            });

        }

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