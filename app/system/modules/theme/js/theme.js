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

    // UIkit overrides
    UIkit.notify.message.defaults.timeout = 2000;

    UIkit.modal.alert = function(content, options) {

        options = UIkit.$.extend(true, {modal:false, title: false, labels:UIkit.modal.labels}, options);

        var modal = UIkit.modal.dialog(([
            options.title ? '<div class="uk-modal-header"><h2>'+options.title+'</h2></div>':'',
            '<div class="uk-margin uk-modal-content">'+String(content)+'</div>',
            '<div class="uk-modal-footer uk-text-right"><button class="uk-button uk-button-link uk-modal-close">'+options.labels.Ok+'</button></div>'
        ]).join(""), options);

        modal.on('show.uk.modal', function(){
            setTimeout(function(){
                modal.element.find('button:first').focus();
            }, 50);
        });

        modal.show();
    };

    UIkit.modal.confirm = function(content, onconfirm, options) {

        onconfirm = UIkit.$.isFunction(onconfirm) ? onconfirm : function(){};
        options   = UIkit.$.extend(true, {modal:false, title: false, labels:UIkit.modal.labels}, options);

        var modal = UIkit.modal.dialog(([
            options.title ? '<div class="uk-modal-header"><h2>'+options.title+'</h2></div>':'',
            '<div class="uk-margin uk-modal-content">'+String(content)+'</div>',
            '<div class="uk-modal-footer uk-text-right"><button class="uk-button uk-button-link uk-modal-close">'+options.labels.Cancel+'</button> <button class="uk-button uk-button-link js-modal-confirm">'+options.labels.Ok+'</button></div>'
        ]).join(""), options);

        modal.element.find(".js-modal-confirm").on("click", function(){
            onconfirm();
            modal.hide();
        });

        modal.on('show.uk.modal', function(){
            setTimeout(function(){
                modal.element.find('button:first').focus();
            }, 50);
        });

        modal.show();
    };

    UIkit.modal.prompt = function(text, value, onsubmit, options) {

        onsubmit = UIkit.$.isFunction(onsubmit) ? onsubmit : function(value){};
        options  = UIkit.$.extend(true, {bgclose:false, keyboard:false, modal:false, title: false, labels:UIkit.modal.labels}, options);

        var modal = UIkit.modal.dialog(([
            options.title ? '<div class="uk-modal-header"><h2>'+options.title+'</h2></div>':'',
            text ? '<div class="uk-modal-content uk-form">'+String(text)+'</div>':'',
            '<div class="uk-margin-small-top uk-modal-content uk-form"><p><input type="text" class="uk-width-1-1"></p></div>',
            '<div class="uk-modal-footer uk-text-right"><button class="uk-button uk-button-link uk-modal-close">'+options.labels.Cancel+'</button> <button class="uk-button uk-button-link js-modal-ok">'+options.labels.Ok+'</button></div>'
        ]).join(""), options),

        input = modal.element.find("input[type='text']").val(value || '').on('keyup', function(e){
            if (e.keyCode == 13) {
                modal.element.find(".js-modal-ok").trigger('click');
            }
        });

        modal.element.find(".js-modal-ok").on("click", function(){
            if (onsubmit(input.val())!==false){
                modal.hide();
            }
        });

        modal.on('show.uk.modal', function(){
            setTimeout(function(){
                input.focus();
            }, 50);
        });

        modal.show();
    };

});
