(function ($, Vue) {

    Vue.component('v-upload', {

        replace: true,

        template: '#upload-tmpl',

        data: function () {
            return {
                pkg: {},
                action: '',
                progress: ''
            };
        },

        ready: function () {

            var settings = {
                action: this.action,
                type: 'json',
                param: 'file',
                loadstart: this.onStart,
                progress: this.onProgress,
                allcomplete: this.onComplete
            };

            UIkit.uploadSelect(this.$$.select, settings);
            UIkit.uploadDrop(this.$$.drop, settings);

            this.modal = UIkit.modal(this.$$.modal);
        },

        methods: {

            onStart: function () {
                this.progress = '1%';
            },

            onProgress: function (percent) {
                this.progress = Math.ceil(percent) + '%';
            },

            onComplete: function (data) {

                var self = this;

                this.progress = '100%';

                setTimeout(function (){
                    self.progress = '';
                }, 250);

                if (data.error) {
                    UIkit.notify(data.error, 'danger');
                    return;
                }

                // $.post(params.api + '/package/' + data.package.name, function (info) {

                //     var version = info.versions[data.package.version];

                //     if (version && version.dist.shasum != data.package.shasum) {
                //         show('checksum-mismatch', upload);
                //     }

                // }, 'jsonp');

                this.modal.show();
            }

        }

    });

})(jQuery, Vue);
