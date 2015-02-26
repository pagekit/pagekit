(function($, UIkit) {

    function install (Vue) {

        Vue.prototype.$date = System.date;
        Vue.prototype.$trans = System.trans;
        Vue.prototype.$transChoice = System.transChoice;
        Vue.prototype.$resource = System.resource;

        /**
         * Filters
         */

        Vue.filter('trans', function(id, parameters, domain, locale) {
            return this.$trans(id, parameters, domain, locale);
        });

        Vue.filter('transChoice', function(id, number, parameters, domain, locale) {
            return this.$transChoice(id, number, parameters, domain, locale);
        });

        Vue.filter('first', function(collection) {
            return Vue.filter('toArray')(collection)[0];
        });

        Vue.filter('length', function(collection) {
            return Vue.filter('toArray')(collection).length;
        });

        Vue.filter('toArray', function(collection) {

            if ($.isPlainObject(collection)) {
                return Object.keys(collection)

                    .filter(function(key) {
                        return key.charAt(0) !== '$';
                    })

                    .map(function(key) {
                        return collection[key];
                    });
            }

            return Array.isArray(collection) ? collection : [];
        });

        Vue.filter('toObject', function(collection) {
            return Array.isArray(collection) ? collection.reduce(function(obj, value, key) {
                obj[key] = value;
                return obj;
            }, {}) : collection;
        });

        /**
         * Pagination component
         */

        Vue.component('v-pagination', {

            data: function() {
                return {
                    page: 1,
                    pages: 1
                };
            },

            replace: true,
            template: '<ul class="uk-pagination"></ul>',

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

        /**
         * Gravatar directive
         */

        Vue.directive('gravatar', {

            update: function (value) {

                var $el = $(this.el), options = { size: ($el.attr('height') || 50) * 2, backup: 'mm', rating: 'g' };

                $el.attr('src', gravatar(value, options));
            }

        });

    }

    if (window.Vue) {
        Vue.use(install);
    }

})(jQuery, UIkit);
