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

            update: function(value) {

                var el = $(this.el), options = { size: (el.attr('height') || 50) * 2, backup: 'mm', rating: 'g' };

                el.attr('src', gravatar(value, options));
            }

        });

        /**
         * Check-all directive
         */

        Vue.directive('check-all', {

            bind: function() {

                var self = this, expr = this.expression, el = $(this.el), root = $(this.vm.$el);

                el.on('change.check-all', function() {
                    $(expr, root).prop('checked', $(this).prop('checked'));
                });

                root.on('change.check-all', expr, function() {

                    var checked = self.checked();

                    if (checked.length === 0) {
                        el.prop('checked', false).prop('indeterminate', false);
                    } else if (checked.length == $(expr, root).length) {
                        el.prop('checked', true).prop('indeterminate', false);
                    } else {
                        el.prop('indeterminate', true);
                    }

                });

            },

            unbind: function() {

                $(this.el).off('.check-all');
                $(this.vm.$el).off('.check-all');

            },

            checked: function() {

                var checked = [];

                $(this.expression, this.vm.$el).each(function() {
                    if ($(this).prop('checked')) {
                        checked.push($(this).val());
                    }
                });

                return checked;
            },

            isLiteral: true
        });

    }

    if (window.Vue) {
        Vue.use(install);
    }

})(jQuery, UIkit);
