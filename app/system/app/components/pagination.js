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
            history.replaceState({page: this.$session.get(this.key)}, '', '?page=' + this.$session.get(this.key));
        }

        var vm = this;
        window.onpopstate = function (event) {

            if (event.state) {
                vm.page = event.state.page;
            } else {
                vm.page = 0;
            }

        };
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
            history.pushState({page: page}, '', '?page=' + page);
            this.$session.set(this.key, page);
        },

        pages: function (pages) {
            this.pagination.render(pages);
        }

    }

};
