module.exports = {

    data: $.extend(true, {
        selected: [],
        config: {filter: {}}
    }, window.$data),

    ready: function () {
        this.load();
    },

    computed: {

        count: function () {
            return this.widgets.length || '';
        },

        positionOptions: function () {
            return [{text: this.$trans('- Assign -'), value: ''}].concat(
                _.map(this.config.positions, function (position) {
                    return {text: this.$trans(position.name), value: position.id};
                }.bind(this))
            );
        }

    },

    methods: {

        getType: function (widget) {
            return _.find(this.config.types, {id: widget.type});
        },

        hasWidgets: function (position) {
            return this.positions[position.id] !== undefined;
        }

    }

};

$(function () {

    (new Widgets(module.exports)).$mount('#widget-index');

});