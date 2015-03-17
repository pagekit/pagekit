jQuery(function ($) {

    var _ = Vue.util.extend({}, Vue.util);

    var vm = new Vue({

        el: '#js-post',

        data: {
            data    : post.data,
            post    : post.data.post
        },

        created: function () {
            this.resource = this.$resource('api/blog/post/:id');
        },

        ready: function() {

            $(this.$el).on('change', function(e) {
                _.trigger(e.target, 'change');
            });

            $(this.$el).on('input', function(e) {
                _.trigger(e.target, 'input');
            });

        },

        computed: {

            statuses: function() {
                return Vue.filter('toArray')($.map(this.data.statuses, function(status, id) { return { text: status, value: id }; }));
            },

            authors: function() {
                return this.data.authors.map(function(user) { return { text: user.username, value: user.id }; });
            },

            date: {

                get: function() {
                    return this.$date('Y-m-d', this.post.date);
                },

                set: function(date) {
                    var prev  = new Date(this.post.date);
                    date = new Date(date);
                    date.setHours(prev.getHours(), prev.getMinutes());
                    this.$set('post.date', date.toISOString());
                }

            },

            time: {

                get: function() {
                    return this.$date('H:i', this.post.date);
                },

                set: function(time) {
                    var date = new Date(this.post.date);
                    date.setHours(time.substr(0, 2), time.substr(3,2));
                    this.$set('post.date', date.toISOString());
                }

            }

        },

        methods: {

            save: function (e) {
                e.preventDefault();

                this.resource.save({ id: this.post.id }, { post: this.post, id: this.post.id }, function (data) {

                    if (data.post) {
                        vm.$set('post', data.post);
                    }

                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            }

        }

    });

    _.trigger = function(el, event) {
        var e = document.createEvent('HTMLEvents');
        e.initEvent(event, false, false);
        el.dispatchEvent(e);
    };

});
