module.exports = {

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
        }, window.$data);
    },

    created: function () {

        this.Comments = this.$resource('api/blog/comment/:id');
        this.config.filter = _.extend({filter: {search: '', status: ''}}, this.config.filter);

        UIkit.init(this.$el);
    },

    watch: {

        'config.page': 'load',

        'config.filter': {
            handler: function () {
                this.load(0);
            },
            deep: true
        }

    },

    computed: {

        statusOptions: function () {

            var options = _.map(this.$data.statuses, function (status, id) {
                return {text: status, value: id};
            });

            return [{label: this.$trans('Filter by'), options: options}];
        }

    },

    methods: {

        active: function (comment) {
            return this.selected.indexOf(comment.id) != -1;
        },

        submit: function () {
            this.save(this.editComment.id ? this.editComment : this.replyComment);
        },

        save: function (comment) {
            return this.Comments.save({id: comment.id}, {comment: comment}, function () {
                this.load();
                this.$notify('Comment saved.');
            }, function (data) {
                this.$notify(data, 'danger');
            });
        },

        status: function (status) {

            var comments = this.getSelected();

            comments.forEach(function (comment) {
                comment.status = status;
            });

            this.Comments.save({id: 'bulk'}, {comments: comments}, function () {
                this.load();
                this.$notify('Comments saved.');
            });
        },

        remove: function () {
            this.Comments.delete({id: 'bulk'}, {ids: this.selected}, function () {
                this.load();
                this.$notify('Comment(s) deleted.');
            });
        },

        load: function (page) {

            page = page !== undefined ? page : this.$get('config.page');

            this.cancel();

            this.Comments.query({filter: this.config.filter, post: this.config.post && this.config.post.id || 0, page: page, limit: this.config.limit}, function (data) {
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
                return vm.selected.indexOf(comment.id) !== -1;
            });
        },

        getStatusText: function (comment) {
            return this.statuses[comment.status];
        },

        cancel: function (e) {

            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            this.$set('replyComment', {});
            this.$set('editComment', {});
        }

    },

    components: {

        'row': {
            inherit: true,
            template: '<component is="{{ editComment.id !== comment.id ? \'default-row\' : \'edit-row\' }}"></component>'
        },

        'default-row': {

            inherit: true,
            template: '#default-row',

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

        },

        'edit-row': {

            inherit: true,
            template: '#edit-row'

        }

    }

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#comments');

});
