window.Widgets = module.exports = {

    data: function () {
        return {
            widgets: [],
            configs: {}
        };
    },

    created: function () {
        this.resource = this.$resource('api/site/widget/:id');
    },

    methods: {

        load: function () {

            return this.resource.query(function (data) {
                this.$set('widgets', data);
            });

        },

        copy: function () {

            var widgets = _.merge([], this.get('selected'));

            widgets.forEach(function (widget) {
                delete widget.id;
            });

            this.resource.save({id: 'bulk'}, {widgets: widgets}, this.load).success(function(){
                this.$set('selected', []);
                UIkit.notify('Widget(s) copied.');
            });
        },

        remove: function () {
            this.resource.delete({id: 'bulk'}, {ids: this.selected}, this.load);
        }

    },

    partials: {

        settings: require('./templates/widget-settings.html')

    },

    components: {

        appearance: require('./components/widget-appearance.vue'),
        visibility: require('./components/widget-visibility.vue')

    }

};
