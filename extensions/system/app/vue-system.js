(function($, UIkit) {

    function install (Vue) {

        /**
         * Methods
         */

        Vue.prototype.$url = System.url;
        Vue.prototype.$resource = System.resource;
        Vue.prototype.$date = Locale.date;
        Vue.prototype.$trans = Locale.trans;
        Vue.prototype.$transChoice = Locale.transChoice;

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

            if (Vue.util.isPlainObject(collection)) {
                return Object.keys(collection)

                    .filter(function(key) {
                        return key.charAt(0) !== '$';
                    })

                    .map(function(key) {
                        return collection[key];
                    });
            }

            return Vue.util.isArray(collection) ? collection : [];
        });

        Vue.filter('toObject', function(collection) {
            return Vue.util.isArray(collection) ? collection.reduce(function(obj, value, key) {
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
                    vm.$set(keypath, self.state());
                });

                vm.$watch(keypath, function(selected) {

                    $(selector, vm.$el).prop('checked', function() {
                        return selected.indexOf($(this).val()) !== -1;
                    });

                    self.state();
                });

            },

            unbind: function() {

                $(this.el).off('.check-all');
                $(this.vm.$el).off('.check-all');

            },

            state: function() {

                var el = $(this.el), checked = this.checked();

                if (checked.length === 0) {
                    el.prop('checked', false).prop('indeterminate', false);
                } else if (checked.length == $(this.expression, this.vm.$el).length) {
                    el.prop('checked', true).prop('indeterminate', false);
                } else {
                    el.prop('indeterminate', true);
                }

                return checked;
            },

            checked: function() {

                var checked = [];

                $(this.expression, this.vm.$el).each(function() {
                    if ($(this).prop('checked')) {
                        checked.push($(this).val());
                    }
                });

                return checked;
            }

        });

        /**
         * Checkbox directive
         */

        Vue.directive('checkbox', {

            twoWay: true,

            bind: function() {

                var vm = this.vm, expression = this.expression, el = $(this.el);

                el.on('change.checkbox', function() {

                    var model = vm.$get(expression), contains = model.indexOf(el.val());

                    if (el.prop('checked')) {
                        if (-1 === contains) {
                            model.push(el.val());
                        }
                    } else if (-1 !== contains) {
                        model.splice(contains, 1);
                    }
                });

            },

            update: function(value) {

                if (undefined === value) {
                    this.set([]);
                    return;
                }

                $(this.el).prop('checked', -1 !== value.indexOf(this.el.value));
            },

            unbind: function() {
                $(this.el).off('.checkbox');
            }

        });

        /**
         * Sticky Table Header directive
         */

        Vue.directive('sticky-table-header', {

            bind: function() {

                var el = $(this.el);
                this.vm.$on('hook:ready', function() {

                    var table = el.css('position', 'relative'),
                        thead = table.find('thead tr'),
                        header = thead.clone().addClass('pk-table-head-sticky').css('z-index', 1).hide(),
                        th = thead.find('th:first'),
                        thclone = header.find('th:first');

                    header.css({ position: 'absolute', top: 0, left: 0 }).appendTo(table);

                    $(window).on('scroll', function() {
                        if (UIkit.Utils.isInView(thead)) {
                            header.hide();
                        } else {
                            thclone.css('width', th.width());
                            header.css({ width: thead.width(), top: window.scrollY - thead.offset().top }).show();
                        }
                    });

                });
            }

        });

    }

    if (window.Vue) {
        Vue.use(install);
    }

})(jQuery, UIkit);
