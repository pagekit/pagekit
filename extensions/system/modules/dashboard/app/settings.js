jQuery(function($) {

    new Vue({

        el: '#dashboard',

        data: $.extend(dashboard, {selected: []}),

        ready: function() {

            var self = this, el = $(this.$el);

            // save widgets order on nestable change
            // el.on('change.uk.nestable', 'ul.uk-nestable', function() {
            //     $.post(self.config.url.reorder, {order: $(this).data('nestable').serialize()}, function(data) {
            //         UIkit.notify(data.message || 'Widgets order updated', 'success');
            //     }).fail(function() {
            //         UIkit.notify('Unable to reorder widgets.', 'danger');
            //     });
            // });

        },

        methods: {

            remove: function() {

                var self = this;

                $.post(this.$url('admin/system/dashboard/delete'), {ids: this.selected}, function(data) {

                    self.$set('widgets', data.widgets);
                    self.$set('selected', []);

                    UIkit.notify(data.message, 'success');
                });
            }

        }

    });

});
