jQuery(function ($) {

    var vm = new Vue({

        el: '#js-settings',

        data: window.$data,

        methods: {

            save: function(e) {

                var data = $(":input", e.target).serialize().parse();

                this.$http.post('admin/system/settings/save', { config: {}, option: { 'hello': $.extend(data.config, this.config) }}, function() {
                     UIkit.notify(vm.$trans('Settings saved.'));
                }).error(function(data) {
                     UIkit.notify(data, 'danger');
                });

            }

        }

    });

});
