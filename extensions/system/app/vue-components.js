(function (UI) {

    Vue.component('pk-pagination', {

        data: function () {
            return {
                page: 1,
                pages: 1
            }
        },

        replace: true,
        template: '<ul class="uk-pagination"></ul>',

        attached: function() {

            var vm = this, pagination = UI.pagination(this.$el, { pages: this.pages });

            pagination.on('select.uk.pagination', function(e, page) {
                vm.$set('page', page);
            });

            this.$watch('page', function (page) {
                pagination.selectPage(page);
            }, true);

            this.$watch('pages', function (pages) {
                pagination.render(pages);
            }, true);

            pagination.selectPage(this.page);
        }
    });

})(UIkit);
