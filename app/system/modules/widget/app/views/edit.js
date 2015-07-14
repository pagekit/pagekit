module.exports = {

    data: function () {
        return _.extend({}, window.$data);
    },

    ready: function () {

        UIkit.tab(this.$$.tab, {connect: this.$$.content});
        // this.$set('widget.data', _.defaults({}, this.widget.data, this.type.defaults));

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
                _.map(this.theme.positions, function (position) {
                    return {text: this.$trans(position.label), value: position.name};
                }, this)
            );
        }

    },

    methods: {

        save: function (e) {
            e.preventDefault();

            this.$broadcast('save', {widget: this.widget});
            this.$resource('api/widget/:id').save({id: this.widget.id}, {widget: this.widget}, function (data) {
                this.$dispatch('saved');

                if (data.widget) {
                    this.$set('widget', data.widget);
                }

                UIkit.notify(data.message);
            });
        },

        cancel: function (e) {
            e.preventDefault();

            this.$dispatch('cancel');
        }

    },

    filters: {

        active: function (sections) {
            return sections.filter(function (section) {
                return !section.active || this.widget.type.match(section.active);
            }, this);
        }

    }

};

$(function () {

    (new Widgets(module.exports)).$mount('#widget-edit');

});
