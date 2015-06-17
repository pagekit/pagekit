module.exports = {

    data: window.$data,

    ready: function () {

        UIkit.tab(this.$$.tab, { connect: this.$$.content });
        // this.$set('widget.settings', _.defaults({}, this.widget.settings, this.type.defaults));

    },

    filters: {

        active: function (sections) {
            return sections.filter(function (section) {
                return !section.active || this.widget.type.match(section.active);
            }.bind(this));
        }

    },

    computed: {

        sections: function () {

            var sections = [];

            _.forIn(this.$options.components, function (component) {
                if (component.options.section) {
                    sections.push(component.options.section);
                }
            });

            return sections;
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

        save: function (e) {

            e.preventDefault();

            var data = { widget: this.widget, config: this.config };

            this.$broadcast('save', data);

            this.$resource('api/widget/:id').save({ id: this.widget.id }, data, function () {
                this.$dispatch('saved');
            });

        },

        cancel: function (e) {

            e.preventDefault();

            this.$dispatch('cancel');

        }

    }

};

$(function () {

    (new Widgets(module.exports)).$mount('#widget-edit');

});