window.Settings = module.exports = {

    data: function () {
        return window.$settings;
    },

    ready: function() {

        UIkit.tab(this.$$.tab, {connect: this.$$.content});

    },

    computed: {

        sections: function () {

            var sections = [], section;

            _.forIn(this.$options.components, function (component, name) {

                section = component.options.section;

                if (section) {
                    section.name = name;
                    sections.push(section);
                }

            });

            return sections;
        }

    },

    methods: {

        save: function(e) {

            e.preventDefault();

            this.$broadcast('save', this.$data);
            this.$resource('admin/system/settings/save').save({ config: this.config, options: this.options }, function() {
                UIkit.notify(this.$trans('Settings saved.'));
            }, function (data) {
                UIkit.notify(data, 'danger');
            });
        }

    },

    components: {

        locale: require('./components/locale.vue'),
        system: require('./components/system.vue')

    }

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#settings');

});
