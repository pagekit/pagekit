window.Widgets = module.exports = {

    data: function () {
        return {
            widgets: []
        };
    },

    created: function () {
        this.resource = this.$resource('api/site/widget{/id}');
    },

    partials: {

        settings: require('./templates/widget-settings.html')

    },

    components: {

        settings: require('./components/widget-settings.vue'),
        visibility: require('./components/widget-visibility.vue')

    }

};
