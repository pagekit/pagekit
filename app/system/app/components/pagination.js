module.exports = {

    template: '<ul class="uk-pagination"></ul>',

    props: ['page', 'pages'],

    data: function() {
        return {
            page: 1,
            pages: 1
        };
    },

    ready: function() {

        var vm = this, pagination = UIkit.pagination(this.$el, { pages: this.pages });

        pagination.on('select.uk.pagination', function(e, page) {
            vm.$set('page', page);
        });

        this.$watch('page', function(page) {
            pagination.selectPage(page);
        }, {immediate: true});

        this.$watch('pages', function(pages) {
            pagination.render(pages);
        }, {deep: true});

    }

};
