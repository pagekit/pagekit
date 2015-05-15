/**
 * Vue Pagination component.
 */

var Vue = require('vue');

Vue.component('v-pagination', {

    replace: true,

    template: '<ul class="uk-pagination"></ul>',

    paramAttributes: ['page', 'pages'],

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
        }, true);

        this.$watch('pages', function(pages) {
            pagination.render(pages);
        }, true);

        pagination.selectPage(this.page);
    }

});
