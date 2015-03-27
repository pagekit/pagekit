jQuery(function($) {

    var vm = new Vue({

        el: '#dashboard',

        data: $.extend($dashboard, {selected: []}),

        ready: function() {

            $(this.$el).on('change.uk.sortable', this.reorder);

        },

        methods: {

            remove: function() {

                $.post(this.$url('admin/system/dashboard/delete'), {ids: this.selected}, function(data) {

                    vm.$set('widgets', data.widgets);
                    vm.$set('selected', []);

                    UIkit.notify(data.message, 'success');
                });
            },

            reorder: function(e, sortable) {

                if (!sortable) return;

                var ordered = vm.$.ordered,
                    order = sortable.element.children().toArray().map(function(el) {
                        return ordered.filter(function(model) { return model.$el == el; })[0].$key;
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
