var $ = require('jquery');
var _ = require('lodash');
var UIkit = require('uikit');

var Settings = Vue.extend({

    sections: [],

    data: function () {
        return window.$settings;
    },

    ready: function() {

        UIkit.tab(this.$$.tab, { connect: this.$$.content})

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

    }

});

Settings.register = function (options) {
    this.component(options.name, options);
    this.options.sections.push(options);
};

Settings.register(require('./components/site.vue'));
Settings.register(require('./components/system.vue'));
Settings.register(require('./components/locale.vue'));

$(function () {

    var settings = new Settings();
    settings.$mount('#settings');

});

module.exports = Settings;
