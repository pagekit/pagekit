var App = Vue.extend({

    data: function () {
        return _.merge({ user: window.$pagekit.user }, window.$data)
    },

    created: function () {

        this.Comments = this.$resource('api/blog/comment/:id');

        this.config.filter = _.extend({ filter: { search: '', status: '' } }, this.config.filter)

        this.$watch('config.page', this.load, false, true);
        this.$watch('config.filter.search', function () { this.load(0); });
        this.$watch('config.filter.status', function () { this.load(0); });
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

        load: function (page) {

            page = page !== undefined ? page : this.$get('config.page');

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

        cancel: function(e) {

            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            this.$broadcast('cancel');
        }

    },

    components: {

        'comments-row': require('./comments-row.vue')

    }

});

jQuery(function () {
    new App().$mount('#comments');
});

module.exports = App;
