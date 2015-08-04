var App = Vue.extend({

    data: function() {
        return {
            data: window.$data,
            post: window.$data.post
        }
    },

    created: function () {
        this.resource = this.$resource('api/blog/post/:id');
    },

    ready: function() {

        var meridian = this.$date(this.post.date, {time:'short'}).match(/(am|pm)/i);

        $('.js-timepicker').each(function(){
            UIkit.timepicker(this, {format: meridian ? '12h':'24h'});
        });
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

                var t = this.$date(this.post.date, {time: 'short'}),
                    h = t.split(':')[0];

                if (h.length == 1) {
                    t = '0'+t;
                }

                return t;
            },

            set: function(time) {

                time = (function(time_str, t, hours, minutes, meridian) {

                    // Convert a string like 11:30 PM to 24h format
                    t        = time_str.match(/(\d+):(\d+) (\w)/);
                    hours    = Number(t[1]);
                    minutes  = Number(t[2]);
                    meridian = t[3].toLowerCase();

                    if (meridian == 'p' && hours < 12) {
                      hours = hours + 12;
                    } else if (meridian == 'a' && hours == 12) {
                      hours = hours - 12;
                    }

                    return [hours, minutes];

                })(time);

                var date = new Date(this.post.date);
                date.setHours(time[0], time[1]);
                this.$set('post.date', date.toISOString());
            }

        }

    },

    methods: {

        save: function (e) {
            e.preventDefault();

            this.resource.save({ id: this.post.id }, { post: this.post, id: this.post.id }, function (data) {

                if (!this.post.id) {
                    window.history.replaceState({}, '', this.$url('admin/blog/post/edit', {id: data.post.id}))
                }

                this.$set('post', data.post);

                UIkit.notify(this.$trans('Post saved.'));

            }, function (data) {
                UIkit.notify(data, 'danger');
            });
        }

    }

});

jQuery(function () {
    new App().$mount('#post');
});

module.exports = App;
