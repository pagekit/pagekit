/*global $data*/

jQuery(function ($) {

    var vm = new Vue({

        el: '#js-widget-edit',

        data: $data,

        created: function () {
            this.Widgets = this.$resource('admin/dashboard/:id');
        },

        methods: {

            save: function (e) {

                e.preventDefault();

                var data = _.merge($(":input", e.target).serialize().parse(), { widget: this.widget });

                this.$broadcast('save', data);

                this.Widgets.save({ id: this.widget.id }, data, function(data) {
                    vm.$set('widget', data);
                });

            }

        }

    });

});
