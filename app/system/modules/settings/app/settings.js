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

        },

        components: {
            'v-locale': locale
        }

    });

    var locale = {

        inherit: true,
        replace: true,

        ready: function() {

            var changed = false;

            this.$watch('adminLocale', function() {
                changed = true;
            }, true);

            this.$on('save', function() {
                if (changed) {
                    window.location.reload();
                }
            }, true);

        },

        computed: {

            adminLocale: function() {
                return this.option['system/locale'].locale_admin;
            }

        }

    };

});
