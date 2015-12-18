window.Settings = {

    el: '#settings',

    data: function () {
        return window.$settings;
    },

    ready: function () {

        UIkit.tab(this.$els.tab, {connect: this.$els.content});

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

        save: function () {
            this.$broadcast('save', this.$data);
            this.$resource('admin/system/settings/save').save({config: this.config, options: this.options}).then(function () {
                        this.$notify('Settings saved.');
                    }, function (res) {
                        this.$notify(res.data, 'danger');
                    }
                );
        }

    },

    components: {

        locale: require('./components/locale.vue'),
        system: require('./components/system.vue')

    }

};

Vue.ready(window.Settings);
