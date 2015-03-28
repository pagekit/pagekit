jQuery(function($) {

    var api = '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&callback=?';

    $('[data-feed]').each(function() {

        var vm = new Vue({

            el: this,

            data: {
                status: 'loading',
                config: {},
                feed: {}
            },

            ready: function() {

                this.$set('config', $(this.$el).data('feed'));

                $.getJSON(api, {q: this.config.url, num: this.config.count}, function(data) {

                    if (data.responseStatus === 200) {
                        vm.$set('feed', data.responseData.feed);
                        vm.$set('status', 'done');
                    } else {
                        vm.$set('status', 'error');
                    }

                }, function() {

                    vm.$set('status', 'error');

                });
            }

        });
    });


});
