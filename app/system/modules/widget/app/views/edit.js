module.exports = {

    data: function () {
        return _.extend({}, window.$data);
    },

    ready: function () {

        UIkit.tab(this.$$.tab, {connect: this.$$.content});
        // this.$set('widget.data', _.defaults({}, this.widget.data, this.type.defaults));

        // set position from get param
        if (!this.widget.id) {
            var match = RegExp('[?&]position=([^&]*)').exec(location.search);
            this.widget.position = (match && decodeURIComponent(match[1].replace(/\+/g, ' '))) || '';
        }
    },

    computed: {

        sections: function () {

            var sections = [];

            _.forIn(this.$options.components, function (component, name) {

                var options = component.options || {}, section = options.section;

                if (section) {
                    section.name = name;
                    sections.push(section);
                }

            });

            return sections;
        },

        positionOptions: function () {
            return _.map(this.config.positions, function (position) {
                return {text: this.$trans(position.label), value: position.name};
            }, this);
        }

    },

    methods: {

        save: function (e) {
            e.preventDefault();

            this.$broadcast('save', {widget: this.widget});
            this.$resource('api/site/widget/:id').save({id: this.widget.id}, {widget: this.widget}, function (data) {
                this.$dispatch('saved');

                if (!this.widget.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/site/widget/edit', {id: data.widget.id}));
                }

                this.$set('widget', data.widget);

                this.$notify('Widget saved.');
            }, function (data) {
                this.$notify(data, 'danger');
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

    },

    mixins: [window.Widgets]

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#widget-edit');

});
