jQuery(function ($) {

    // main menu
    var menu = new Vue({

        inherit: true,

        el: '#sidebar',

        data: _.extend({
            navContent: null,
            navSetup: null,
            item: null,
            subnav: null
        }, window.$pagekit),

        created: function () {

            var menu = _(this.menu).sortBy('priority').groupBy('parent').value();
            var item = _.find(menu.root, 'active');
            var vm = this;

            if (this.compactMode == 'true') {
                $('body').addClass('compact');
            }

            this.$set('navContent', menu.root.filter(function(menuItem) {
                if (item && menuItem.label == item.label) {
                    menuItem.class = 'active';
                } else {
                    menuItem.class = 'inactive';
                }

                return !menuItem.setupSection;
            }));

            this.$set('navSetup', menu.root.filter(function(menuItem) {
                if (item && menuItem.label == item.label) {
                    menuItem.class = 'active';
                } else {
                    menuItem.class = 'inactive';
                }

                return menuItem.setupSection;
            }));

            if (item) {
                this.$set('item', item);
                this.$set('subnav', menu[item.id]);
            }

            // content menu order
            $('#js-appnav-content').on('stop.uk.sortable', function () {

                var data = {};

                $(this).children().each(function (i) {
                    data[$(this).data('id')] = i;
                });

                $('#js-appnav-setup').children().each(function (i) {
                    data[$(this).data('id')] = i;
                });

                vm.$http.post('admin/adminmenu', {order: data}, function () {
                    // message?
                });
            });

            // setup menu order
            $('#js-appnav-setup').on('stop.uk.sortable', function () {

                var data = {};

                $('#js-appnav-content').children().each(function (i) {
                    data[$(this).data('id')] = i;
                });

                $(this).children().each(function (i) {
                    data[$(this).data('id')] = i;
                });

                vm.$http.post('admin/adminmenu', {order: data}, function () {
                    // message?
                });
            });
        }

    });

    // current page title
    var title = new Vue({

        inherit: true,

        el: '#header',

        data: _.extend({
            item: null,
        }, window.$pagekit),

        created: function () {

            var menu = _(this.menu).sortBy('priority').groupBy('parent').value();
            var item = _.find(menu.root, 'active');

            if (item) {
                this.$set('item', item);
                this.$set('subnav', menu[item.id]);
            }

        }

    });

    // offcanvas menu
    menu.$addChild({el: '#offcanvas', inherit: true});
    menu.$addChild({el: '#offcanvas-flip', inherit: true});

    // show system messages
    UIkit.notify.message.defaults.timeout = 2000;
    $('.pk-system-messages').children().each(function () {

        var message = $(this), data = message.data();

        // remove success message faster
        if (data.status && data.status == 'success') {
            data.timeout = 2000;
        }

        UIkit.notify(message.html(), data);
        message.remove();
    });

    // UIkit overrides
    UIkit.modal.alert = function (content, options) {

        options = UIkit.$.extend(true, {modal: false, title: false, labels: UIkit.modal.labels}, options);

        var modal = UIkit.modal.dialog(([
            options.title ? '<div class="uk-modal-header"><h2>' + options.title + '</h2></div>' : '',
            '<div class="uk-margin uk-modal-content">' + (options.title ? content : '<h2>' + content + '</h2>') + '</div>',
            '<div class="uk-modal-footer uk-text-right"><button class="uk-button uk-button-link uk-modal-close">' + options.labels.Ok + '</button></div>'
        ]).join(""), options);

        modal.on('show.uk.modal', function () {
            setTimeout(function () {
                modal.element.find('button:first').focus();
            }, 50);
        });

        modal.show();
    };

    UIkit.modal.confirm = function (content, onconfirm, options) {

        onconfirm = UIkit.$.isFunction(onconfirm) ? onconfirm : function () {
        };
        options = UIkit.$.extend(true, {modal: false, title: false, labels: UIkit.modal.labels}, options);

        var modal = UIkit.modal.dialog(([
            options.title ? '<div class="uk-modal-header"><h2>' + options.title + '</h2></div>' : '',
            '<div class="uk-margin uk-modal-content">' + (options.title ? content : '<h2>' + content + '</h2>') + '</div>',
            '<div class="uk-modal-footer uk-text-right"><button class="uk-button uk-button-link uk-modal-close">' + options.labels.Cancel + '</button> <button class="uk-button uk-button-link js-modal-confirm">' + options.labels.Ok + '</button></div>'
        ]).join(""), options);

        modal.element.find(".js-modal-confirm").on("click", function () {
            onconfirm();
            modal.hide();
        });

        modal.on('show.uk.modal', function () {
            setTimeout(function () {
                modal.element.find('button:first').focus();
            }, 50);
        });

        modal.show();
    };

    UIkit.modal.prompt = function (text, value, onsubmit, options) {

        onsubmit = UIkit.$.isFunction(onsubmit) ? onsubmit : function (value) {
        };
        options = UIkit.$.extend(true, {modal: false, title: false, labels: UIkit.modal.labels}, options);

        var modal = UIkit.modal.dialog(([
                options.title ? '<div class="uk-modal-header"><h2>' + options.title + '</h2></div>' : '',
                text ? '<div class="uk-modal-content uk-form">' + (options.title ? text : '<h2>' + text + '</h2>') + '</div>' : '',
                '<div class="uk-margin-small-top uk-modal-content uk-form"><p><input type="text" class="uk-width-1-1"></p></div>',
                '<div class="uk-modal-footer uk-text-right"><button class="uk-button uk-button-link uk-modal-close">' + options.labels.Cancel + '</button> <button class="uk-button uk-button-link js-modal-ok">' + options.labels.Ok + '</button></div>'
            ]).join(""), options),

            input = modal.element.find("input[type='text']").val(value || '').on('keyup', function (e) {
                if (e.keyCode == 13) {
                    modal.element.find(".js-modal-ok").trigger('click');
                }
            });

        modal.element.find(".js-modal-ok").on("click", function () {
            if (onsubmit(input.val()) !== false) {
                modal.hide();
            }
        });

        modal.on('show.uk.modal', function () {
            setTimeout(function () {
                input.focus();
            }, 50);
        });

        modal.show();
    };

    // sidebar mode toggle
    $('#sidebar-mode-toggle').on('click', function () {
        $('body').toggleClass('compact');

        $.post('admin/adminmenu', {compactMode: String($('body').hasClass('compact'))}, function (data) {
            // EMPTY!
        });
    });
});
