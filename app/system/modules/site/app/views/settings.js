window.Site = module.exports = {

    data: function () {
        return _.merge({form: {}}, window.$data);
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

            this.$broadcast('save', this.config);

            this.$http.post('admin/system/settings/config', { name: 'system/site', config: this.config }, function() {
                 this.$notify('Settings saved.');
            }).error(function(data) {
                 this.$notify(data, 'danger');
            });

        }

    },

    components: {

        'site-code': require('../components/site-code.vue'),
        'site-general': require('../components/site-general.vue'),
        'site-maintenance': require('../components/site-maintenance.vue')

    }

};

$(function () {

    new Vue(module.exports).$mount('#settings');

});
