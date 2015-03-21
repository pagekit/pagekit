jQuery(function ($) {

    // upload package
    var progressbar = $(".js-upload-progressbar"),
        bar         = $('.uk-progress-bar', progressbar),
        dialog      = $('.js-upload-modal', upload),
        settings    = {

        action: upload.data("action"),
        type  : 'json',
        params: system.csrf.params,
        param : 'file',

        loadstart: function() {
            bar.css("width", "0%").text("0%");
            progressbar.removeClass("uk-hidden");
        },

        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css("width", percent+"%").text(percent+"%");
        },

        allcomplete: function(data) {

            bar.css("width", "100%").text("100%");

            setTimeout(function(){
                progressbar.addClass("uk-hidden");
            }, 250);

            if (data.error) {
                UIkit.notify(data.error, 'danger');
                return;
            }

            $.post(params.api + '/package/' + data.package.name, function(info) {

                var version = info.versions[data.package.version];

                if (version && version.dist.shasum != data.package.shasum) {
                    show('checksum-mismatch', upload);
                }

            }, 'jsonp');

            dialog.html(tmpl.render('package.upload', data));

            if (!modal) {
                modal = UIkit.modal(dialog);
            }

            modal.show();
        }
    };

    // upload objects
    UIkit.uploadSelect($(".js-upload-select"), settings);
    UIkit.uploadDrop($(".js-upload-drop"), settings);

});
