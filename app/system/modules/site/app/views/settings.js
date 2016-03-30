window.Site = {

    el: '#settings',

    data: function () {
        return _.merge({form: {}}, window.$data);
    },

    ready: function () {

        UIkit.tab(this.$els.tab, {connect: this.$els.content});

    },

    computed: {

        sections: function () {

            var sections = [], hash = window.location.hash.replace('#', '');

            _.forIn(this.$options.components, function (component, name) {

                var options = component.options || {}, section = options.section;

                if (section) {
                    section.name = name;
                    section.active = name == hash;
                    sections.push(section);
                }

            });

            return sections;
        }

    },

    methods: {

        save: function () {
            this.$broadcast('save', this.config);

            this.$http.post('admin/system/settings/config', {name: 'system/site', config: this.config}).then(function () {
                        this.$notify('Settings saved.');
                    }, function (res) {
                        this.$notify(res.data, 'danger');
                    });
        }

    },

    components: {

        'site-code': require('../components/site-code.vue'),
        'site-meta': require('../components/site-meta.vue'),
        'site-general': require('../components/site-general.vue'),
        'site-maintenance': require('../components/site-maintenance.vue')

    }

};

Vue.ready(window.Site);
