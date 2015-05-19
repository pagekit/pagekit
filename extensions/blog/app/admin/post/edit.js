/*global $data*/

jQuery(function ($) {

    new Vue({

        el: '#js-post',

        data: {
            data: $data,
            post: $data.post
        },

        created: function () {
            this.resource = this.$resource('api/blog/post/:id');
        },

        computed: {

            statuses: function() {
                return _.map(this.data.statuses, function(status, id) { return { text: status, value: id }; } );
            },

            authors: function() {
                return this.data.authors.map(function(user) { return { text: user.username, value: user.id }; });
            },

            date: {

                get: function() {
                    return this.$date(this.post.date, 'short');
                },

                set: function(date) {
                    var prev = new Date(this.post.date);
                    date = new Date(date);
                    date.setHours(prev.getHours(), prev.getMinutes());
                    this.$set('post.date', date.toISOString());
                }

            },

            time: {

                get: function() {
                    return this.$date(this.post.date, {time: 'short'});
                },

                set: function(time) {
                    var date = new Date(this.post.date);
                    date.setHours(time.substr(0, 2), time.substr(3, 2));
                    this.$set('post.date', date.toISOString());
                }

            }

        },

        methods: {

            save: function (e) {
                e.preventDefault();

                this.resource.save({ id: this.post.id }, { post: this.post, id: this.post.id }, function (data) {

                    if (data.post) {
                        this.$set('post', data.post);
                    }

                    UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
                });
            }

        }

    });

});
