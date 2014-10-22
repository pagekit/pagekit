require(['jquery', 'uikit!form-password'], function($, uikit) {

    var Installer = {

        current   : false,
        resetanims: "uk-animation-shake uk-animation-slide-right uk-animation-slide-left uk-animation-reverse",

        init: function(container) {

            var $this = this;

            this.installer    = $(container);
            this.configexists = this.installer.data('config');
            this.steps        = this.installer.find('[data-step]').hide().removeClass('uk-hidden');

            this.steps.each(function() {

                var el = $(this), step = el.data("step");

                el.find("form").data("step", step);

            }).on("submit", "form", function(e) {
                e.preventDefault();

                var frm = $(this);

                frm.find(".uk-form-danger").removeClass('uk-form-danger').end().find(".uk-form-help-block").hide();

                $this["on" + frm.data("step")](frm);
            });

            this.gotoStep('start');
        },
        gotoStep: function(step) {

            var cls         = 'uk-animation-slide-right',
                next        = this.steps.filter('[data-step="' + step + '"]').removeClass('uk-animation-slide-right'),
                container   = next.find(".js-panel:first").removeClass(this.resetanims),
                curr        = false,
                nxtcallback = function(fn) { fn(); };

            if (this.current) {
                curr        = this.steps.filter('[data-step="' + this.current + '"]');
                nxtcallback = function(fn) { nxtcallback = fn; };
            }

            nxtcallback(function() {
                next.show();
                container.addClass(cls).width();
                next.find(":input:first").focus();
            });

            if (curr) {
                curr.find(".js-panel:first").one("webkitAnimationEnd oanimationend oAnimationEnd msAnimationEnd animationend", function() {
                    curr.hide();
                    nxtcallback();
                }).removeClass(this.resetanims).addClass("uk-animation-slide-left uk-animation-reverse").width();
            }

            this.current = step;

            if (step == "finish") {
                this.onfinish();
            }
        },
        install: function(fn) {
            return $.post(this.installer.data("route"), this.installer.find("form").serialize(), fn, 'json');
        },

        // form submission callbacks
        onstart: function() {
            this.gotoStep(this.configexists ? 'user' : 'database');
        },
        ondatabase: function(frm) {

            var $this = this;

            frm.parent().removeClass(this.resetanims);

            function displayDbError(msg) {
                if (!frm.find('.uk-alert-danger').length) {
                    frm.prepend('<div class="uk-alert uk-alert-danger uk-margin"><p>' + msg + '</p></div>');
                } else {
                    frm.find('.uk-alert-danger').find('p').html(msg);
                }

                // shake the installer
                frm.parent()
                    .removeClass("uk-animation-shake")
                    .addClass("uk-animation-shake");
            };

            $.post(frm.attr('action'), frm.serialize(), function(data) {

                if (data && data.status) {

                    switch (data.status) {
                        case "no-connection":

                            displayDbError(data.message);

                            break;
                        case "tables-exist":

                            displayDbError(data.message);

                            break;
                        case "no-tables":
                            $this.gotoStep('user');
                            break;
                    }

                } else {
                    alert("Whoops, something went wrong");
                }

            }, "json");
        },
        onuser: function() {
            this.gotoStep(this.configexists ? 'finish' : 'site');
        },
        onsite: function(frm) {
            this.gotoStep('finish');
        },
        onfinish: function() {

            var $this = this, element = this.steps.filter('[data-step="finish"]'), status = element.find("[data-status]").hide();

            status.filter('[data-status="install"]').show();

            setTimeout(function() {

                $this.install(function(data) {

                    status.hide();

                    switch (data.status) {

                        case "success":

                            status.filter('[data-status="finished"]').show();

                            break;

                        default:

                            var con = status.filter('[data-status="fail"]');

                            con.find(".js-error-message").html(data.message);

                            if (data.status == "db-sql-failed") {
                                //TODO: missing
                            }

                            con.show();
                    }

                }).fail(function() {
                    var con = status.hide().filter('[data-status="fail"]');
                    con.find(".js-error-message").html("Whoops, something went wrong!").end().show();
                });

            }, 1500);

        }
    };

    // on domready
    $(function() {

        // toggle db driver
        $('#form-dbdriver').on('change', function() {

            var value = $(this).val();
            $('.js-database').each(function() {
                var connection = $(this), hide = !connection.is('.js-'+value);
                connection.toggleClass('uk-hidden', hide).find(':input').prop('disabled', hide)
            });

        }).trigger('change');

        // prevent input html5 validation bubble
        $(':input').bind('invalid', function(e) {
            e.preventDefault();

            var input = $(this), form = $(this.form);

            form.find(":valid.uk-form-danger").each(function(){
                var valid = $(this);

                valid.removeClass('uk-form-danger').nextUntil().filter('.uk-form-help-block').hide();

                if(valid.data("errorMessage")) {
                    $(valid.data("errorMessage")).hide();
                }
            });

            input.addClass("uk-form-danger").focus().nextUntil().filter('.uk-form-help-block').show();

            if(input.data("errorMessage")) {
                $(input.data("errorMessage")).show();
            }

            if(form.is(":visible")) {
                form.parent().removeClass("uk-animation-shake").width(); // prepare for animation
                form.parent().addClass("uk-animation-shake");
            }

        });

        $("form").find(".uk-form-help-block").hide();

        Installer.init("#installer");

        window.Installer = Installer;
    });

});
