module.exports = {

    template: '<ul class="uk-pagination"></ul>',

    props: {
        page: {
            default: 0
        },

        pages: {
            default: 1
        },
        
        replaceState: {
            type: Boolean,
            default: true
        }
    },

    created: function () {

        this.key = this.$parent.$options.name + '.pagination';

        if (this.page === null && this.$session.get(this.key)) {
            this.$set('page', this.$session.get(this.key));
        }

        if (this.replaceState) {
            this.$state('page', this.page);
        }

    },

    ready: function () {

        var vm = this;

        this.pagination = UIkit.pagination(this.$el, {pages: this.pages, currentPage: this.page || 0});
        this.pagination.on('select.uk.pagination', function (e, page) {
            vm.$set('page', page);
        });

    },

    watch: {

        page: function (page) {
            this.pagination.selectPage(page || 0);
            this.$session.set(this.key, page || 0);
        },

        pages: function (pages) {
            this.pagination.render(pages);
        }

    }

};
