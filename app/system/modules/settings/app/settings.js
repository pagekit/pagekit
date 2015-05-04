var $ = require('jquery');
var _ = require('lodash');
var UIkit = require('uikit');

var Settings = Vue.extend({

    data: function () {
        return _.merge({

            sections: [],
            labels: [],
            config: {},
            options: {}

        }, window.$settings)
    },

    created: function () {

        var self = this;

        _(this.$options.components).pairs()
            .filter(function(component) {
                return component[1].options.isSection;
            }).sortBy(function(component) {
                return component[1].options.priority;
            }).value().forEach(function(component) {
                self.labels.push(component[1].options.label);
                self.sections.push(component[0]);
            });
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

                self.$broadcast('saved');
            });

        }

    }

});

Settings.register = function (name, options) {
    options.isSection = true;
    this.options.components[name] = Vue.extend(options);
};

Settings.register('site', require('./components/site.vue'));
Settings.register('system', require('./components/system.vue'));
Settings.register('locale', require('./components/locale.vue'));

$(function () {

    var settings = new Settings();
    settings.$mount('#settings');

});

module.exports = Settings;
