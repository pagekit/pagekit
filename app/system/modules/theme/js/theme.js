jQuery(function ($) {

    // main menu
    var menu = new Vue({

        el: '#header',

        data: _.extend({}, window.$pagekit),

        ready: function () {

            var menu = _(this.menu).sortBy('priority').groupBy('parent').value();
            var item = _.find(menu.root, 'active');
            var self = this;

            this.$add('nav', menu.root);

            if (item) {
                this.$add('item', item);
                this.$add('subnav', menu[item.id]);
            }

            // main menu order
            $(this.$$.appnav).on('stop.uk.sortable', function () {

                var data = {};

                $(this).children().each(function (i) {
                    data[$(this).data('id')] = i;
                });

                self.$http.post('admin/adminmenu', {order:data}, function() {
                    // message?
                });
            });
        }

    });

    // offcanvas menu
    menu.$addChild({el: '#offcanvas', inherit: true});
    menu.$addChild({el: '#offcanvas-flip', inherit: true});

    UIkit.notify.message.defaults.timeout = 2000;

});
