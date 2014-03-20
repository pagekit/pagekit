define(['jquery', 'tmpl!link.types'], function($, tmpl) {

    var form  = $(tmpl.get('link.types')),
        forms = $('[data-type]', form),
        types = $('.js-types', form),
        url   = $('.js-url', form),
        edit  = $('.js-edit', form);

    edit.on('update.linkpicker', function(e, params, link) {
        link = link ? link : $('.js-types', form).val();

        url.val(link + (params ? '?' + params : ''));
    });

    form.on('change', '.js-types' ,function() {

        var type = $(this).val()+'' , show;

        forms.addClass('uk-hidden').hide().filter('[data-type="'+type+'"]').removeClass('uk-hidden').show();

        // hide edit form if empty
        show = forms.not('.uk-hidden').children(':not(script)').length > 0;
        edit.toggleClass('uk-hidden', !show).toggle(show);

        edit.trigger('change.linkpicker', [deparam(url.val().split('?')[1] + ''), url.val(), type]);
    });

    url.on('change', function() {

        var types = $('.js-types', form), type = $(this).val().split('?')[0];

        type = $('option[value="'+type+'"]', types).length ? type : '';

        types.val(type).trigger('change');
    });

    var Link  = function(element, options) {

        var $this = this;

        this.options = $.extend({}, Link.defaults, options);

        this.element = element;

        this.types = types.clone().find('option').each(function() {
            if ($this.options.typeFilter && -1 !== $.inArray($(this).val(), $this.options.typeFilter)) {
                $(this).remove();
            }
        }).end();
    };

    $.extend(Link.prototype, {

        init: function(link) {

            this.element = form.appendTo(this.element);

            $('.js-types', form).replaceWith(this.types);

            url.val(link).trigger('change');
        },

        getValue: function() {
            return url.val();
        }
    });

    Link.defaults = {
        typeFilter: []
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

    return Link;
});