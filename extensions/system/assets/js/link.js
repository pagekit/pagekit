define(['jquery', 'require'], function($, req) {

    var Link = function(options) {

        var $this = this;

        this.options = $.extend({}, Link.defaults, options);
        this.edit    = $(this.options.editForm);
        this.types   = $(this.options.typeField);
        this.url     = $(this.options.urlField);
        this.forms   = {};

        $.getJSON(this.options.url)
            .done(function(links) {
                $.each(links, function(route, link) {

                    if ($this.options.typeFilter && -1 !== $.inArray(route, $this.options.typeFilter)) {
                        return;
                    }

                    $this.types.append($('<option>', { value: route, text : link.label }));
                    $this.forms[route] = link.form;
                });
                $this.init();
            })
            .fail(function() {
                // TODO: handle error
            });

        this.types.on('change', function() {

            var type = $(this).val()+'', show;

            $this.clearEditForm();

            if (!$this.forms[type]) {
                return;
            }

            $this.edit.html($this.forms[type]);

            show = $this.edit.children(':not(script)').length > 0;

            $this.edit.toggleClass('uk-hidden', !show).toggle(show);

            $this.triggerLoad();
        });

        this.url.on('change', function() {

            var type = $(this).val().split('?')[0];

            type = $('option[value="'+type+'"]', $this.types).length ? type : '';

            if ($this.types.val() != type) {
                $this.types.val(type).trigger('change');
            } else {
                $this.triggerLoad();
            }
        });
    };

    $.extend(Link.prototype, {

        init: function() {
            this.url.trigger('change');
        },

        triggerLoad: function() {
            $(document).trigger('load.urlpicker', [this, this.deparam(this.url.val().split('?')[1] + ''), this.url.val()]);
        },

        clearEditForm: function() {
            this.edit.html('');
        },

        updateUrl: function(params, url) {
            url = url ? url : this.types.val();

            this.url.val(url + (params ? '?' + $.param(params) : ''));
        },

        /*
         * https://github.com/chrissrogers/jquery-deparam
         */
        deparam: function(params) {
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

    });

    Link.defaults = {
        urlField:   '.js-link-url',
        typeField:  '.js-link-types',
        typeFilter: [],
        editForm:   '.js-link-edit',
        url:        req.toUrl('admin/system/links')
    };

    return Link;
});