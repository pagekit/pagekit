define(['jquery', 'require'], function($, req) {

    var deferreds = {}, data = {};

    var Link = function(element, options) {

        this.options = $.extend({}, Link.defaults, options);
        this.element = $(element);

        var $this   = this,
            context = this.options.context;

        data[context] = data[context] || $.post(this.options.url, { context: context });

        data[context].done(function(data) {

            $this.element.html(data);

            $this.types  = {};
            $this.link   = $('.js-url', this.element);
            $this.select = $('.js-types', $this.element);
            $this.edit   = $('.js-edit', $this.element);
            $this.forms  = $this.edit.children('[data-type]');

            var init = [];

            $this.forms.each(function() {

                var form = $(this), type = form.data('type');

                if (-1 != $.inArray(type, $this.options.filter)) {
                    $this._getOption(type).remove();
                    return;
                }

                if (!deferreds[type]) {
                    deferreds[type] = $.Deferred();
                }

                deferreds[type].done(function(func) {
                    $this.types[type] = func($this, form);
                });

                init.push(deferreds[type]);
            });

            $this.select.on('change', function() {
                var type = $this._show($(this).val());
                if (type) type.update();
            });

            $this.link.on('change', function() {
                $this._show($this._getOption(this.value.split('?', 1)[0]).val());
            });

            $.when.apply($, init).done(function() {
                $this.set('', $this.options.value);
            });
        });
    };

    $.extend(Link.prototype, {

        set: function(params, url) {
            this.link.val((url || this.select.val()) + (params ? '?' + params : '')).trigger('change');
        },

        get: function() {
            return this.link.val();
        },

        _getOption: function(type) {
            var options = this.select.children(), option;

            option = options.filter('[value="'+type+'"]');

            if (!option.length) {
                option = options.filter('[value=""]');
            }

            return option;
        },

        _show: function(type) {
            var form = this.forms.addClass('uk-hidden').hide().filter('[data-type="'+type+'"]').removeClass('uk-hidden').show(),
                show = form.children(':not(script)').length > 0,
                url  = this.link.val();

            this.edit.toggleClass('uk-hidden', !show).toggle(show);

            if (!this.types[type]) return;

            this.select.val(type);

            try {

                var params = deparam(url.split('?')[1] + '');

            } catch (e) { params = {}; }

            this.types[type].show(params, url);

            return this.types[type];
        }

    });

    Link.defaults = {
        filter: [],
        url   : req.toUrl('index.php/admin/system/link'),
        value : ''
    };

    /*
     * https://github.com/chrissrogers/jquery-deparam
     */
    function deparam(params) {
        var obj = {};

        $.each(params.replace(/\+/g, ' ').split('&'), function(j, v) {

            var param = v.split('='), key = decodeURIComponent(param[0]), val, cur = obj, i = 0, keys = key.split(']['), keys_last = keys.length - 1;

            if (/\[/.test(keys[0]) && /\]$/.test(keys[keys_last])) {

                keys[keys_last] = keys[keys_last].replace(/\]$/, '');
                keys = keys.shift().split('[').concat(keys);

                keys_last = keys.length - 1;

            } else {
                keys_last = 0;
            }

            if (param.length === 2) {
                val = decodeURIComponent(param[1]);

                if (keys_last) {
                    for (; i <= keys_last; i++) {
                        key = keys[i] === '' ? cur.length : keys[i];
                        cur = cur[key] = i < keys_last ? cur[key] || (keys[i + 1] && isNaN(keys[i + 1]) ? {} : []) : val;
                    }

                } else {

                    if ($.isArray(obj[key])) {
                        obj[key].push(val);
                    } else if (obj[key] !== undefined) {
                        obj[key] = [obj[key], val];
                    } else {
                        obj[key] = val;
                    }
                }

            } else if (key) {
                obj[key] = '';
            }
        });

        return obj;
    }

    return {

        attach: function(element, options) {
            return new Link(element, options);
        },

        register: function(name, type) {

            if (!deferreds[name]) {
                deferreds[name] = $.Deferred();
            }

            deferreds[name].resolve(type);
        }

    };
});