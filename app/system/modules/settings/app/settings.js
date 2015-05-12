var $ = require('jquery');
var _ = require('lodash');
var UIkit = require('uikit');

var Settings = Vue.extend({

    data: function () {
        return window.$settings;
    },

    computed: {

        sections: function () {

            var sections = [];

            _.each(this.$options.components, function (component) {
                if (component.options.section) {
                    sections.push(component.options.section);
                }
            });

            return sections;
        }

    },

    ready: function() {

        UIkit.tab(this.$$.tab, {connect: this.$$.content});

    },

    methods: {

        save: function(e) {

            e.preventDefault();

            var self = this;

            this.$broadcast('save', this.$data);

            this.$resource('admin/system/settings/save').save({ config: this.config, options: this.options }, function() {

                UIkit.notify(self.$trans('Settings saved.'));

            }, function (data) {

                UIkit.notify(data, 'danger');
            });

        }

    },

    components: {
        'settings-locale': require('./components/locale.vue'),
        'settings-site':   require('./components/site.vue'),
        'settings-system': require('./components/system.vue')
    }

});

$(function () {

    new Settings().$mount('#settings');

});

module.exports = Settings;
