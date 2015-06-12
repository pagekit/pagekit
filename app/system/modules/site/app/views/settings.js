var $ = require('jquery');
var UIkit = require('uikit');

var Settings = Vue.extend({

    data: window.$data,

    methods: {

        save: function(e) {

            this.$http.post('admin/system/settings/config', { name: 'system/site', config: this.config }, function() {
                 UIkit.notify(vm.$trans('Settings saved.'));
            }).error(function(data) {
                 UIkit.notify(data, 'danger');
            });

        }

    }

});

$(function () {

    new Settings().$mount('#settings');

});

module.exports = Settings;
