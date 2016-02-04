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

    created: function () {

        this.key = this.$parent.$options.name + '.pagination';

        if (this.page === undefined && this.$session.get(this.key)) {
            this.$set('page', this.$session.get(this.key));
        } else if (this.page === undefined) {
            this.page = 0;
        }

        this.$state('page', this.page ? this.page : undefined);

    },

    ready: function () {

        var vm = this;

        this.pagination = UIkit.pagination(this.$el, {pages: this.pages, currentPage: this.page});
        this.pagination.on('select.uk.pagination', function (e, page) {
            vm.$set('page', page);
        });

    },

    watch: {

        page: function (page) {
            this.pagination.selectPage(page);
            this.$session.set(this.key, page);
        },

        pages: function (pages) {
            this.pagination.render(pages);
        }

    }

};
