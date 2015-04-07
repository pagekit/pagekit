(function($) {

    function install (Vue) {

        var config = $pagekit, templates = {};

        /**
         * Config
         */

        Vue.url.root = config.url;
        Vue.http.options.emulateHTTP = true;
        Vue.http.headers.common['X-XSRF-TOKEN'] = config.csrf;
        Vue.http.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        /**
         * Methods
         */

        Vue.template = function(name) {

            if (!templates[name]) {
                templates[name] = Vue.http.get(Vue.url('system/tmpl/:template', {template: name}));
            }

            return templates[name];
        };

        Vue.url.static = function(url, params, root) {

            if (!root) {
                root = Vue.url.root;
            }

            return Vue.url(url, params, root.replace(/\/index.php$/i, ''));
        };

        Vue.prototype.$date = Locale.date;
        Vue.prototype.$trans = Locale.trans;
        Vue.prototype.$transChoice = Locale.transChoice;

        /**
         * TODO remove with vuejs > 0.11.5
         */
        Vue.prototype.$toOptions = function(collection) {
            return Vue.filter('toOptions')(collection);
        };

        var getTemplate = function(options, fn, args) {

            if (!options.template) {
                return fn.call(this, args);
            }

            var self     = this,
                template = options.template,
                frag     = Vue.parsers.template.parse(template);

            if (frag) {
                options.template = frag;
                return fn.call(this, args);
            }

            Vue.template(template.slice(1)).success(function(tmpl) {
                options.template = tmpl;
            }).always(function() {
                fn.call(self, args);
            });

            return this;
        };

        var $mount = Vue.prototype.$mount;
        Vue.prototype.$mount = function(el) {
            return getTemplate.call(this, this.$options, $mount, el);
        };

        var component = Vue.directive('component'), bind = component.bind;
        component.bind = function() {
            return getTemplate.call(this, this.vm.$options.components[this.expression].options, bind);
        };

        var partial = Vue.directive('partial'), insert = partial.insert;
        partial.insert = function(id) {

            var self = this, partial = this.vm.$options.partials[id];

            if (undefined === id || partial) {
                return insert.call(this, id);
            }

            var frag = Vue.parsers.template.parse(id);

            if (frag) {
                this.vm.$options.partials[id] = frag;
                return insert.call(this, id);
            }

            Vue.template(id.slice(1)).success(function(tmpl) {
                self.vm.$options.partials[id] = tmpl;
            }).always(function() {
                insert.call(self, id);
            });
        };

        /**
         * Filters
         */

        Vue.filter('baseUrl', function(url) {
            return _.startsWith(url, config.url) ? url.substr(config.url.length) : url;
        });

        Vue.filter('trans', function(id, parameters, domain, locale) {
            return this.$trans(id, evalExp.call(this, parameters), evalExp.call(this, domain), evalExp.call(this, locale));
        });

        Vue.filter('transChoice', function(id, number, parameters, domain, locale) {
            return this.$transChoice(id, evalExp.call(this, number) || 0, evalExp.call(this, parameters), evalExp.call(this, domain), evalExp.call(this, locale));
        });

        Vue.filter('date', function(date, format) {
            return this.$date(format, date);
        });

        Vue.filter('first', function(collection) {
            return Vue.filter('toArray')(collection)[0];
        });

        Vue.filter('length', function(collection) {
            return Vue.filter('toArray')(collection).length;
        });

        Vue.filter('toArray', function(collection) {

            if (Vue.util.isPlainObject(collection)) {
                return Object.keys(collection).map(function(key) {
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

        Vue.filter('toOptions', function toOptions(collection) {
            return Object.keys(collection).map(function (key) {

                var op = collection[key];
                if (typeof op === 'string') {
                    return { text: op, value: key };
                } else {
                    return { label: key, options: toOptions(op) }
                }

            });
        });

        var evalExp = function(expression) {

            try {

                return undefined === expression ? expression : Vue.parsers.expression.parse(expression).get.call(this, this);

            } catch (e) {
                if (Vue.config.warnExpressionErrors) {
                    Vue.util.warn('Error when evaluating expression "' + expression + '":\n   ' + e);
                }
            }

        };

        /**
         * Components
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
         * Directives
         */

        Vue.directive('gravatar', {

            update: function(value) {

                var el = $(this.el), options = { size: (el.attr('height') || 50) * 2, backup: 'mm', rating: 'g' };

                el.attr('src', gravatar(value || '', options));
            }

        });

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

                this.unbindWatcher = vm.$watch(keypath, function(selected) {

                    $(selector, vm.$el).prop('checked', function() {
                        return selected.indexOf($(this).val()) !== -1;
                    });

                    self.state();
                });

            },

            unbind: function() {

                $(this.el).off('.check-all');
                $(this.vm.$el).off('.check-all');

                if (this.unbindWatcher) {
                    this.unbindWatcher();
                }
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

        Vue.directive('sticky-table-header', {

            bind: function() {

                var el = $(this.el);

                this.handler = function() {

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

                };

                this.vm.$on('hook:ready', this.handler);
            },

            unbind: function() {
                this.vm.$off('hook:ready', this.handler);
            }

        });

        /**
         * Utilities
         */

        Vue.util.findBy = function (arr, key, val) {

            var value;

            for (var i = 0; i < arr.length; i++) {

                value = arr[i];

                if (value.hasOwnProperty(key) && value[key] === val) {
                    return value;
                }
            }

            return undefined;
        };

    }

    if (window.Vue) {
        Vue.use(install);
    }

    /**
     * Copyright (c) 2013 Kevin van Zonneveld (http://kvz.io) and Contributors (http://phpjs.org/authors)
     */

    String.prototype.parse = function (array) {

        var strArr = this.replace(/^&/, '').replace(/&$/, '').split('&'),
            sal = strArr.length,
            i, j, ct, p, lastObj, obj, lastIter, undef, chr, tmp, key, value,
            postLeftBracketPos, keys, keysLen,
            fixStr = function(str) {
                return decodeURIComponent(str.replace(/\+/g, '%20'));
            };

        if (!array) {
            array = {};
        }

        for (i = 0; i < sal; i++) {
            tmp = strArr[i].split('=');
            key = fixStr(tmp[0]);
            value = (tmp.length < 2) ? '' : fixStr(tmp[1]);

            while (key.charAt(0) === ' ') {
                key = key.slice(1);
            }
            if (key.indexOf('\x00') > -1) {
                key = key.slice(0, key.indexOf('\x00'));
            }
            if (key && key.charAt(0) !== '[') {
                keys = [];
                postLeftBracketPos = 0;
                for (j = 0; j < key.length; j++) {
                    if (key.charAt(j) === '[' && !postLeftBracketPos) {
                        postLeftBracketPos = j + 1;
                    }
                    else if (key.charAt(j) === ']') {
                        if (postLeftBracketPos) {
                            if (!keys.length) {
                                keys.push(key.slice(0, postLeftBracketPos - 1));
                            }
                            keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
                            postLeftBracketPos = 0;
                            if (key.charAt(j + 1) !== '[') {
                                break;
                            }
                        }
                    }
                }
                if (!keys.length) {
                    keys = [key];
                }
                for (j = 0; j < keys[0].length; j++) {
                    chr = keys[0].charAt(j);
                    if (chr === ' ' || chr === '.' || chr === '[') {
                        keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
                    }
                    if (chr === '[') {
                        break;
                    }
                }

                obj = array;
                for (j = 0, keysLen = keys.length; j < keysLen; j++) {
                    key = keys[j].replace(/^['"]/, '').replace(/['"]$/, '');
                    lastIter = j !== keys.length - 1;
                    lastObj = obj;
                    if ((key !== '' && key !== ' ') || j === 0) {
                        if (obj[key] === undef) {
                            obj[key] = {};
                        }
                        obj = obj[key];
                    }
                    else { // To insert new dimension
                        ct = -1;
                        for (p in obj) {
                            if (obj.hasOwnProperty(p)) {
                                if (+p > ct && p.match(/^\d+$/g)) {
                                    ct = +p;
                                }
                            }
                        }
                        key = ct + 1;
                    }
                }
                lastObj[key] = value;
            }
        }

        return array;
    }

})(jQuery);
