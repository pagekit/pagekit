/**
 * Vue Directives
 */

var $ = require('jquery');
var md5 = require('md5');
var Vue = require('vue');

Vue.directive('gravatar', {

    update: function(value) {

        var el = $(this.el), url = '//gravatar.com/avatar/', params = [];

        params.push('r=g');
        params.push('d=mm');
        params.push('s=' + (el.attr('height') || 50) * 2);

        el.attr('src', url + md5(value) + '?' + params.join('&'));
    }

});

Vue.directive('check-all', {

    isLiteral: true,

    bind: function() {

        var self = this, vm = this.vm, el = $(this.el), keypath = this.arg, selector = this.expression;

        el.on('change.check-all', function() {
            $(selector, vm.$el).prop('checked', $(this).prop('checked'));
            vm.$set(keypath, self.checked());
        });

        $(vm.$el).on('change.check-all', selector, function() {
            vm.$set(keypath, self.state());
        });

        $(vm.$el).on('click.check-all', '.check-item', function(e) {

            if (!$(e.target).is(':input, a') && !window.getSelection().toString()) {
                $(this).find(selector).trigger('click');
            }
        });

        this.unbindWatcher = vm.$watch(keypath, function(selected) {

            $(selector, vm.$el).prop('checked', function() {
                return selected.indexOf($(this).val()) !== -1;
            });

            self.state();
        });

    },

    unbind: function() {

        $(this.el).off('.check-all');
        $(this.vm.$el).off('.check-all');

        if (this.unbindWatcher) {
            this.unbindWatcher();
        }
    },

    state: function() {

        var el = $(this.el), checked = this.checked();

        if (checked.length === 0) {
            el.prop('checked', false).prop('indeterminate', false);
        } else if (checked.length == $(this.expression, this.vm.$el).length) {
            el.prop('checked', true).prop('indeterminate', false);
        } else {
            el.prop('indeterminate', true);
        }

        return checked;
    },

    checked: function() {

        var checked = [];

        $(this.expression, this.vm.$el).each(function() {
            if ($(this).prop('checked')) {
                checked.push($(this).val());
            }
        });

        return checked;
    }

});

Vue.directive('checkbox', {

    twoWay: true,

    bind: function() {

        var vm = this.vm, expression = this.expression, el = $(this.el);

        el.on('change.checkbox', function() {

            var model = vm.$get(expression), contains = model.indexOf(el.val());

            if (el.prop('checked')) {
                if (-1 === contains) {
                    model.push(el.val());
                }
            } else if (-1 !== contains) {
                model.splice(contains, 1);
            }
        });

    },

    update: function(value) {

        if (undefined === value) {
            this.set([]);
            return;
        }

        $(this.el).prop('checked', -1 !== value.indexOf(this.el.value));
    },

    unbind: function() {
        $(this.el).off('.checkbox');
    }

});

Vue.directive('order', {

    bind: function () {

        var self = this;

        this.dir       = '';
        this.active    = false;
        this.indicator = $('<i class="uk-icon-justify uk-margin-small-left"></i>');

        $(this.el).addClass('pk-table-order uk-visible-hover-inline').on('click.order', function(){

            self.dir = (self.dir == 'asc') ? 'desc':'asc';
            self.vm.$set(self.expression, [self.arg, self.dir].join(' '));

        }).append(this.indicator);
    },

    update: function (data) {

        var parts = data.split(' '),
            field = parts[0],
            dir   = parts[1] || 'asc';

        this.indicator.removeClass('pk-icon-arrow-up pk-icon-arrow-down');

        if (field == this.arg) {
            this.active = true;
            this.dir    = dir;

            this.indicator.removeClass('uk-invisible').addClass(dir == 'asc' ? 'pk-icon-arrow-down':'pk-icon-arrow-up');
        } else {
            this.indicator.addClass('pk-icon-arrow-down uk-invisible');
            this.active = false;
            this.dir    = '';
        }
    },

    unbind: function() {
        $(this.el).removeClass('pk-table-order').off('.order');
        this.indicator.remove();
    }
});


Vue.directive('confirm', {

    bind: function () {

        var el = this.el, self = this, _ = Vue.util, buttons = (_.attr(el, 'buttons') || '').split(',');

        this.options = {
            title:false,
            text: this.vm.$trans('Are you sure?'),
            labels: {Ok: (this.vm.$trans(buttons[0] || 'Ok')), Cancel:(this.vm.$trans(buttons[1] || 'Cancel'))}
        };

        this. dirs = this.vm._directives.filter(function (dir) {
            return dir.name == 'on' && dir.el === el;
        });

        this.dirs.forEach(function (dir) {

            _.off(dir.el, dir.arg, dir.handler);
            _.on(dir.el, dir.arg, function (e) {

                UIkit.modal.confirm(self.vm.$trans(self.options.text), function() {
                    dir.handler(e);
                }, self.options);
            });
        });
    },

    update: function(value) {

        if(this.arg && value) {  // vue-confirm="'Title':'Text...?'"
            this.options.title = this.arg;
            this.options.text = value;
        }else if(typeof(value) === 'string') { // vue-confirm="'Text...?'"
            this.options.text = value;
        } else if(typeof(value) === 'object') { // vue-confirm="{title:'Title', text:'Text...?'}"
            this.options = $.extend(this.options, value);
        }
    },

    unbind: function() {
        this.dirs.forEach(function (dir) {
            Vue.util.off(dir.el, dir.arg, dir.handler);
        });
    }
});
