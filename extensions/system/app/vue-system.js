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

            isLiteral: true,

            bind: function() {

                var self = this, vm = this.vm, el = $(this.el), keypath = this.arg, selector = this.expression;

                el.on('change.check-all', function() {
                    $(selector, vm.$el).prop('checked', $(this).prop('checked'));
                    vm.$set(keypath, self.checked());
                });

                $(vm.$el).on('change.check-all', selector, function() {
                    self.state();
                    vm.$set(keypath, self.checked());
                });

                vm.$watch(keypath, function(selected) {
                    $(selector, vm.$el).each(function() {
                        var el = $(this);
                        el.prop('checked', -1 !== selected.indexOf(el.val()));
                    });

                    self.state();
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

            state: function() {

                var checked = this.checked(), el = $(this.el);

                if (checked.length === 0) {
                    el.prop('checked', false).prop('indeterminate', false);
                } else if (checked.length == $(this.expression, this.vm.$el).length) {
                    el.prop('checked', true).prop('indeterminate', false);
                } else {
                    el.prop('indeterminate', true);
                }
            }

        });

    }

    if (window.Vue) {
        Vue.use(install);
    }

})(jQuery, UIkit);
