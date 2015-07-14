module.exports = Vue.extend({

    data: function () {
        return _.merge({
            posts: [],
            comments: false,
            pages: 0,
            count: '',
            selected: [],
            user: window.$pagekit.user,
            replyComment: {},
            editComment: {}
        }, window.$data)
    },

    created: function () {

        this.Comments = this.$resource('api/blog/comment/:id');
        this.config.filter = _.extend({ filter: { search: '', status: '' } }, this.config.filter);

        UIkit.init(this.$el);
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
        }

    },

    methods: {

        active: function (comment) {
            return this.selected.indexOf(comment.id.toString()) != -1;
        },

        submit: function () {
            this.save(this.editComment.id ? this.editComment : this.replyComment);
        },

        save: function (comment) {
            return this.Comments.save({ id: comment.id }, { comment: comment }, function () {
                this.load();
                UIkit.notify(this.$trans('Comment saved.'));
            }, function (data) {
                UIkit.notify(data, 'danger');
            });
        },

        status: function (status) {

            var comments = this.getSelected();

            comments.forEach(function (comment) {
                comment.status = status;
            });

            this.Comments.save({ id: 'bulk' }, { comments: comments }, function (data) {
                this.load();
                UIkit.notify(this.$trans('Comments saved.'));
            });
        },

        remove: function () {
            this.Comments.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                this.load();
                UIkit.notify(this.$trans('Comment(s) deleted.'));
            });
        },

        load: function (page) {

            page = page !== undefined ? page : this.$get('config.page');

            this.cancel();

            this.Comments.query({ filter: this.config.filter, post: this.config.post && this.config.post.id || 0, page: page, limit: this.config.limit }, function (data) {
                this.$set('posts', data.posts);
                this.$set('comments', data.comments);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('config.page', page);
                this.$set('selected', []);
            });
        },

        getSelected: function () {
            var vm = this;
            return this.comments.filter(function (comment) {
                return vm.selected.indexOf(comment.id.toString()) !== -1;
            });
        },

        getStatusText: function (comment) {
            return this.statuses[comment.status];
        },

        cancel: function(e) {

            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            this.$set('replyComment', {});
            this.$set('editComment', {});
        }

    },

    components: {

        row: {

            replace: false,
            inherit: true,

            computed: {

                post: function () {
                    return _.find(this.posts, 'id', this.comment.post_id) || {};
                }

            },

            methods: {

                reply: function () {
                    this.cancel();
                    this.$set('replyComment', {parent_id: this.comment.id, post_id: this.comment.post_id, author: this.user.name, email: this.user.email});
                },

                edit: function () {
                    this.cancel();
                    this.$set('editComment', _.merge({}, this.comment));
                },

                toggleStatus: function () {
                    this.comment.status = this.comment.status === 1 ? 0 : 1;
                    this.save(this.comment);
                }

            }

        }

    }

});

jQuery(function () {
    (new module.exports()).$mount('#comments');
});
