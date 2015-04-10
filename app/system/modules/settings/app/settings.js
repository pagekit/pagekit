jQuery(function ($) {

    var vm = new Vue({

        el: '#settings',

        data: $.extend({ config: {}, option: {} }, window.$settings),

        methods: {

            save: function(e) {

                e.preventDefault();

                var data = $(":input", e.target).serialize().parse();

                this.$resource('admin/system/settings/save').save({ config: $.extend(data.config, this.config), option: $.extend(data.option, this.option) }, function() {
                    UIkit.notify(vm.$trans('Settings saved.'), 'success');

                    vm.$broadcast('save');
                });

            }

        }

    });

});
