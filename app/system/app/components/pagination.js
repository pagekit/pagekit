module.exports = {

    template: '<ul class="uk-pagination"></ul>',

    props: {
        page: {
            type: Number,
            default: 0
        },

        pages: {
            type: Number,
            default: 1
        }
    },

    ready: function() {

        var vm = this;

        this.pagination = UIkit.pagination(this.$el, { pages: this.pages, currentPage: this.page });

        this.pagination.on('select.uk.pagination', function(e, page) {
            vm.$set('page', page);
        });

    },

    watch: {

        page: function(page) {
            this.pagination.selectPage(page);
        },

        pages: function(pages) {
            this.pagination.render(pages);
        }

    }

};
