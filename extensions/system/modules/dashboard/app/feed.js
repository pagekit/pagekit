jQuery(function($) {

    var api = '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&callback=?';

    $('[data-feed]').each(function() {

        new Vue({

            el: this,

            data: {
                status: 'loading',
                config: {},
                feed: {}
            },

            ready: function() {

                var self = this;

                this.$set('config', $(this.$el).data('feed'));

                $.getJSON(api, {q: this.config.url, num: this.config.count}, function(data) {

                    if (data.responseStatus == 200) {
                        self.$set('feed', data.responseData.feed);
                        self.$set('status', 'done');
                    } else {
                        self.$set('status', 'error');
                    }

                }, function() {

                    self.$set('status', 'error');

                });
            }

        });
    });


});