module.exports = {

    data: window.$data,

    methods: {

        save: function() {

            this.$http.post('admin/system/settings/config', { name: 'system/site', config: this.config }, function() {
                 UIkit.notify(this.$trans('Settings saved.'));
            }).error(function(data) {
                 UIkit.notify(data, 'danger');
            });

        }

    }

};

$(function () {

    new Vue(module.exports).$mount('#settings');

});
