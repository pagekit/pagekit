define(['jquery', 'require'], function($, req) {

    var deferreds = {}, data = {};

    var Link = function(element, options) {

        this.options = $.extend({}, Link.defaults, options);
        this.element = $(element);
        this.link    = false;

        var $this   = this,
            context = this.options.context;

        data[context] = data[context] || $.post(this.options.url, { context: context });

        data[context].done(function(data) {

            $this.element.html(data);

            $this.types = $('.js-types', $this.element);

            var init   = [], types  = {},
                $edit  = $('.js-edit', $this.element),
                $forms = $edit.children('[data-type]');

            $forms.each(function() {

                var form = $(this),
                    type = form.data('type');

                if (-1 != $.inArray(type, $this.options.filter)) {
                    $this.types.find('option[value="'+type+'"]').remove();
                    return;
                }

                if (!deferreds[type]) {
                    deferreds[type] = $.Deferred();
                }

                deferreds[type].done(function(func) {
                    types[type] = func($this, form);
                });

                init.push(deferreds[type]);
            });

            $this.types.on('change', function() {

                var type = $(this).val(),
                    form = $forms.addClass('uk-hidden').hide().filter('[data-type="'+type+'"]').removeClass('uk-hidden').show(),
                    show = form.children(':not(script)').length > 0;

                $edit.toggleClass('uk-hidden', !show).toggle(show);

                types[type].show(deparam($this.link.split('?')[1] + ''), $this.link);
            });

            $.when.apply($, init).done(function() {
                $this.set('', $this.options.value);
            });
        });
    };

    $.extend(Link.prototype, {

        set: function(params, url) {

            url = (url || this.types.val()) + (params ? '?' + params : '');

            if (this.link === url) return;

            this.link = url;

            var type = url.split('?')[0];
            this.types.val($('option[value="'+type+'"]', this.types).length ? type : '').trigger('change');
        },

        get: function() {
            return this.link;
        }

    });

    Link.defaults = {
        filter: [],
        url   : req.toUrl('admin/system/link'),
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