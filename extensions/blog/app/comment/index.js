/*global $config, $data*/

jQuery(function ($) {

    var vm = new Vue({

        el: '#js-comments',

        data: $.extend(window.$data, {
            comments: null,
            pages: 0,
            count: '',
            selected: [],
            editing: null
        }),

        created: function () {

            this.resource = this.$resource('api/blog/comment/:id');
            this.config.filter = $.extend({ status: '' }, this.config.filter ? this.config.filter : {});

            this.$watch('config.page', this.load, true, true);
            this.$watch('config.filter', function() { this.load(0); }, true);
        },

        computed: {

            statuses: function() {
                return Vue.filter('toArray')($.map(this.$data.statuses, function(status, id) { return { text: status, value: id }; }));
            },

            statusesFilter: function () {

                var options = _.map(this.$data.statuses, function (status, id) {
                    return { text: status, value: id };
                });

                return [{ text: this.$trans('Status'), value: '' }, { label: this.$trans('Filter by'), options: options }];
            }

        },

        methods: {

            submit: function (e) {
                e.preventDefault();

                if (this.editing) {
                    this.save(this.editing);
                }
            },

            save: function (comment) {
                this.resource.save({ id: comment.id }, { comment: comment }, function (data) {
                    vm.load();
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
                });
            },

            status: function(status) {

                var comments = this.getSelected();

                comments.forEach(function(comment) {
                    comment.status = status;
                });

                this.resource.save({ id: 'bulk' }, { comments: comments }, function (data) {
                    vm.load();
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : '');
                });
            },

            remove: function() {
                this.resource.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                    vm.load();
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

                this.resource.query({ filter: this.config.filter, post: this.config.post && this.config.post.id || 0, page: page }, function (data) {
                    vm.$set('comments', data.comments);
                    vm.$set('pages', data.pages);
                    vm.$set('count', data.count);
                    vm.$set('config.page', page);
                    vm.$set('selected', []);
                });
            },

            getSelected: function() {
                return this.comments.filter(function(comment) { return vm.selected.indexOf(comment.id.toString()) !== -1; });
            },

            getStatusText: function(comment) {
                return this.data.statuses[comment.status];
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

});
