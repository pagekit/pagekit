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

    computed: {

        statuses: function() {
            return _.map(this.data.statuses, function(status, id) { return { text: status, value: id }; } );
        },

        authors: function() {
            return this.data.authors.map(function(user) { return { text: user.username, value: user.id }; });
        }

    },

    methods: {

        save: function (e) {
            e.preventDefault();

            this.resource.save({ id: this.post.id }, { post: this.post, id: this.post.id }, function (data) {

                if (!this.post.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/blog/post/edit', {id: data.post.id}))
                }

                this.$set('post', data.post);

                this.$notify('Post saved.');

            }, function (data) {
                this.$notify(data, 'danger');
            });
        }

    }

});

jQuery(function () {
    new App().$mount('#post');
});

module.exports = App;
