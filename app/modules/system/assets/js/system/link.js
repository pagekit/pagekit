define(['jquery', 'system'], function($, system) {

    var data = {};

    var Link = function(element, options) {

        this.options = $.extend({}, Link.defaults, options);
        this.element = $(element);

        var $this = this, context = this.options.context;

        data[context] = data[context] || $.post(this.options.url, { context: context });

        data[context].done(function(data) {

            $this.element.html(data);

            $this.link  = $('.js-url', $this.element);
            $this.types = $('.js-types', $this.element);
            $this.edit  = $('.js-edit', $this.element);

            $this.types.on('change', function() {
                $this._update($(this).val());
            });

            $this.link.on('change', function() {
                $this._update($(this).val());
            });

            $this.set($this.options.value);
        });
    };

    $.extend(Link.prototype, {

        set: function(url) {
            this.link.val(url).trigger('change');
        },

        get: function() {
            return this.link.val();
        },

        _update: function(link) {

            var $this = this;
            $.post(this.options.url + '/form', { link: link, context: this.options.context }, function(data) {

                $this.types.val(data.type ? data.type : '');
                $this.edit.empty().append(data.form).toggleClass('uk-hidden', !$this.edit.children(':not(script)').length);

            }, 'json');

        }

    });

    Link.defaults = {
        url    : system.config.link,
        value  : '',
        context: ''
    };

    system.link = function(element, options) {
        return new Link(element, options);
    };

    return system;
});