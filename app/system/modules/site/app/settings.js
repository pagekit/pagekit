jQuery(function ($) {

    var vm = new Vue({

        el: '#settings',

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

});
