module.exports = Vue.extend({

    data: function() {
        return _.merge({
            posts: false,
            pages: 0,
            count: '',
            selected: [],
            canEditAll: false
        }, window.$data);
    },

    created: function () {

        this.resource = this.$resource('api/blog/post/:id');
        this.config.filter = _.extend({ search: '', status: '' , author:'', order: 'date desc'}, this.config.filter);

    },

    watch: {

        'config.page': 'load',

        'config.filter': {
            handler: function () { this.load(0); },
            deep: true
        }

    },

    computed: {

        statusOptions: function () {

            var options = _.map(this.$data.statuses, function (status, id) {
                return { text: status, value: id };
            });

            return [{ text: this.$trans('Status'), value: '' }, { label: this.$trans('Filter by'), options: options }];
        },

        authors: function() {

            var options = _.map(this.$data.authors, function (author) {
                return { text: author.name, value: author.user_id };
            });

            return [{ text: this.$trans('Author'), value: '' }, { label: this.$trans('Filter by'), options: options }];
        }
    },

    methods: {

        active: function (post) {
            return this.selected.indexOf(post.id.toString()) != -1;
        },

        save: function (post) {
            this.resource.save({ id: post.id }, { post: post }, function (data) {
                this.load();
                UIkit.notify(this.$trans('Post saved.'));
            });
        },

        status: function(status) {

            var posts = this.getSelected();

            posts.forEach(function(post) {
                post.status = status;
            });

            this.resource.save({ id: 'bulk' }, { posts: posts }, function (data) {
                this.load();
                UIkit.notify(this.$trans('Posts saved.'));
            });
        },

        remove: function() {

            this.resource.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                this.load();
                UIkit.notify(this.$trans('Posts deleted.'));
            });
        },

        toggleStatus: function (post) {
            post.status = post.status === 2 ? 3 : 2;
            this.save(post);
        },

        copy: function() {

            if (!this.selected.length) {
                return;
            }

            this.resource.save({ id: 'copy' }, { ids: this.selected }, function (data) {
                this.load();
                UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
            });
        },

        load: function (page) {

            page = page !== undefined ? page : this.config.page;

            this.resource.query({ filter: this.config.filter, page: page }, function (data) {
                this.$set('posts', data.posts);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('config.page', page);
                this.$set('selected', []);
            });
        },

        getSelected: function() {
            return this.posts.filter(function(post) { return this.selected.indexOf(post.id.toString()) !== -1; }, this);
        },

        getStatusText: function(post) {
            return this.statuses[post.status];
        }

    }

});

jQuery(function () {
    (new module.exports()).$mount('#post');
});
