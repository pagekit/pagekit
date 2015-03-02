jQuery(function($) {

    var vm = new Vue({

        el: '#dashboard',

        data: $.extend(dashboard, {selected: []}),

        ready: function() {

            $(this.$el).on('change.uk.nestable', 'ul.uk-nestable', this.reorder);

        },

        methods: {

            remove: function() {

                $.post(this.$url('admin/system/dashboard/delete'), {ids: this.selected}, function(data) {

                    vm.$set('widgets', data.widgets);
                    vm.$set('selected', []);

                    UIkit.notify(data.message, 'success');
                });
            },

            reorder: function() {

                var order = $.map($('[data-uk-nestable]', this.$el).data('nestable').serialize(), function(data) {
                    return data.id;
                });

                $.post(this.$url('admin/system/dashboard/reorder'), {order: order}, function(data) {
                    UIkit.notify(data.message || vm.$trans('Widgets order updated'), 'success');
                }).fail(function() {
                    UIkit.notify(vm.$trans('Unable to reorder widgets.'), 'danger');
                });
            }

        }

    });

});
