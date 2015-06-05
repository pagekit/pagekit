var App = Vue.extend({

    data: function () {
        return _.merge({ user: window.$pagekit.user }, window.$data)
    },

    created: function () {

        this.Comments = this.$resource('api/blog/comment/:id');
        this.config.filter = _.extend({ status: '' }, this.config.filter ? this.config.filter : {});

        this.$watch('config.page', this.load, false, true);
        this.$watch('config.filter', function () { this.load(0); }, true);
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

        save: function (comment) {

            if (!this.editing) {
                return;
            }

            this.Comments.save({ id: comment.id }, { comment: comment }, function (data) {
                this.load();
                UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
            });
        },

        status: function (status) {

            var comments = this.getSelected();

            comments.forEach(function (comment) {
                comment.status = status;
            });

            this.Comments.save({ id: 'bulk' }, { comments: comments }, function (data) {
                this.load();
                UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
            });
        },

        remove: function () {
            this.Comments.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                this.load();
                UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
            });
        },

        toggleStatus: function (comment) {
            comment.status = comment.status === 1 ? 0 : 1;
            this.save(comment);
        },

        load: function (page) {

            page = page !== undefined ? page : this.config.page;

            this.cancel();

            this.Comments.query({ filter: this.config.filter, post: this.config.post && this.config.post.id || 0, page: page }, function (data) {
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

        reply: function(comment) {
            this.cancel();

            this.$set('editing', { parent_id: comment.id });
            this.comments.splice(this.comments.indexOf(comment) + 1, 0, this.editing);
        },

        edit: function(comment) {
            this.cancel();

            this.$set('editing', Vue.util.extend({}, comment));
            this.comments.splice(this.comments.indexOf(comment), 0, this.editing);
        },

        cancel: function(e) {

            if (e) {
                e.preventDefault();
            }

            if (this.editing) {
                this.comments.splice(this.comments.indexOf(this.editing), 1);
                this.$set('editing', null);
            }
        }

    }

});

jQuery(function () {
    new App().$mount('#comments');
});

module.exports = App;
