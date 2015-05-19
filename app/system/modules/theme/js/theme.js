jQuery(function ($) {

    // main menu
    var menu = new Vue({

        el: '#header',

        data: _.extend({}, window.$pagekit),

        ready: function () {

            var menu = _(this.menu).sortBy('priority').groupBy('parent').value();
            var item = _.find(menu.root, 'active');

            this.$add('nav', menu.root);

            if (item) {
                this.$add('item', item);
                this.$add('subnav', menu[item.id]);
            }
        }

    });

    // offcanvas menu
    menu.$addChild({

        el: '#offcanvas',

        inherit: true

    });

    // main menu order
    // $('.js-menu').on('stop.uk.sortable', function () {

    //     var data = {};

    //     $(this).children().each(function (i) {
    //         data[$(this).data('id')] = i;
    //     });

    //     $.post($(this).data('url'), {'order': data}, function () {
    //         // message ?
    //     });
    // });

    UIkit.notify.message.defaults.timeout = 2000;

});