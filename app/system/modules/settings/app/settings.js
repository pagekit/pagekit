window.Settings = module.exports = {

    data: function () {
        return window.$settings;
    },

    ready: function() {

        UIkit.tab(this.$$.tab, {connect: this.$$.content});

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
        }

    },

    methods: {

        save: function(e) {

            e.preventDefault();

            this.$broadcast('save', this.$data);
            this.$resource('admin/system/settings/save').save({ config: this.config, options: this.options }, function() {
                this.$notify('Settings saved.');
            }, function (data) {
                this.$notify(data, 'danger');
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
