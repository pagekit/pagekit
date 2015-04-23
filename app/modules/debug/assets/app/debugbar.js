(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
jQuery(function ($) {

  $('body').append('<div id="profiler"></div>');

  var opts = require('./debugbar.vue');
  var app  = new Vue(opts).$mount('#profiler');

});

},{"./debugbar.vue":10}],2:[function(require,module,exports){
var __vue_template__ = "<div v-el=\"navbar\" style=\"display: none\">\n\n        <a title=\"User\"><div class=\"pf-icon pf-icon-auth\" v-class=\"pf-parent: user\"></div> {{ label }}</a>\n\n        <div class=\"pf-dropdown\" v-show=\"user\">\n\n            <table class=\"pf-table pf-table-dropdown\">\n                <tbody>\n                    <tr>\n                        <td>Username</td>\n                        <td>{{ user }}</td>\n                    </tr>\n                    <tr>\n                        <td>Roles</td>\n                        <td>{{ roles | json }}</td>\n                    </tr>\n                    <tr>\n                        <td>Authenticated</td>\n                        <td>{{ authenticated ? 'yes' : 'no' }}</td>\n                    </tr>\n                    <tr>\n                        <td>Class</td>\n                        <td>{{ user_class }}</td>\n                    </tr>\n                </tbody>\n            </table>\n\n        </div>\n\n    </div>";
module.exports = {

    ready: function () {
      this.$parent.add(this, $(this.$$.navbar).html());
    },

    computed: {

        label: function () {

            if (this.user) {
                return this.user;
            }

            return this.enabled ? 'You are not authenticated.' : 'Authentication is disabled.';
        }

    }

  };
;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;

},{}],3:[function(require,module,exports){
var __vue_template__ = "<h1>Queries</h1>\n\n    <p v-show=\"!nb_statements\">\n        <em>No queries.</em>\n    </p>\n\n    <div v-repeat=\"statements\">\n\n        <pre><code>{{ sql }}</code></pre>\n\n        <p class=\"pf-submenu\">\n            <span>{{ duration_str }}</span>\n            <span>{{ params | json }}</span>\n        </p>\n\n    </div>\n\n    <div v-el=\"navbar\" style=\"display: none\">\n\n        <a title=\"Database\" class=\"pf-parent\">\n            <div class=\"pf-icon pf-icon-database\"></div> {{ nb_statements }}\n        </a>\n\n        <div class=\"pf-dropdown\">\n\n            <table class=\"pf-table pf-table-dropdown\">\n                <tbody>\n                    <tr>\n                        <td>Queries</td>\n                        <td>{{ nb_statements }}</td>\n                    </tr>\n                    <tr>\n                        <td>Time</td>\n                        <td>{{ accumulated_duration_str }}</td>\n                    </tr>\n                    <tr>\n                        <td>Driver</td>\n                        <td>{{ driver }}</td>\n                    </tr>\n                </tbody>\n            </table>\n\n        </div>\n\n    </div>";
module.exports = {

    ready: function () {
      this.$parent.add(this, $(this.$$.navbar).html(), 'database');
    }

  };
;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;

},{}],4:[function(require,module,exports){
var __vue_template__ = "";
module.exports = {

    ready: function () {
      this.$parent.add(this, '<a title="Events"><div class="pf-icon pf-icon-events"></div> Events</a>');
    }

  };

},{}],5:[function(require,module,exports){
var __vue_template__ = "";
module.exports = {

    ready: function () {
      this.$parent.add(this, '<a title="Memory"><div class="pf-icon pf-icon-memory"></div> {{ peak_usage_str }}</a>');
    }

  };

},{}],6:[function(require,module,exports){
var __vue_template__ = "";
module.exports = {

    ready: function () {
      this.$parent.add(this, '<a title="Request"><div class="pf-icon pf-icon-request"></div> <span class="pf-badge">200</span> @test</a>');
    }

  };

},{}],7:[function(require,module,exports){
var __vue_template__ = "<h1>Routes</h1>\n\n    <table class=\"pf-table\">\n        <thead>\n            <tr>\n                <th>Name</th>\n                <th>Pattern</th>\n                <th>Controller</th>\n            </tr>\n        </thead>\n        <tbody>\n            <tr v-repeat=\"routes\">\n                <td>{{ name }}</td>\n                <td>{{ pattern }} {{ methods | str }}</td>\n                <td><abbr title=\"{{ controller }}\">{{ controller | short }}</abbr></td>\n            </tr>\n        </tbody>\n    </table>";
module.exports = {

    ready: function () {
      this.$parent.add(this, '<a title="Routes"><div class="pf-icon pf-icon-routes"></div> Routes</a>', 'routes');
    },

    filters: {

        str: function (methods) {
            return methods.length ? '(' + methods + ')' : '';
        },

        short: function (controller) {
            return controller.split('\\').pop();
        }

    }

  };
;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;

},{}],8:[function(require,module,exports){
var __vue_template__ = "<h1>Information</h1>\n\n    <h2>System</h2>\n    <table class=\"pf-table pf-table-dropdown\">\n        <tbody>\n            <tr>\n                <td>Pagekit</td>\n                <td>{{ version }}</td>\n            </tr>\n            <tr>\n                <td>Server</td>\n                <td>{{ server }}</td>\n            </tr>\n            <tr>\n                <td>Useragent</td>\n                <td>{{ useragent }}</td>\n            </tr>\n        </tbody>\n    </table>\n\n    <h2>PHP</h2>\n    <table class=\"pf-table pf-table-dropdown\">\n        <tbody>\n            <tr>\n                <td>PHP</td>\n                <td>{{ phpversion }}</td>\n            </tr>\n            <tr>\n                <td>PHP SAPI</td>\n                <td>{{ sapi_name }}</td>\n            </tr>\n            <tr>\n                <td>System</td>\n                <td>{{ php }}</td>\n            </tr>\n            <tr>\n                <td>Extensions</td>\n                <td>{{ extensions }}</td>\n            </tr>\n        </tbody>\n    </table>\n\n    <h2>Database</h2>\n    <table class=\"pf-table pf-table-dropdown\">\n        <tbody>\n            <tr>\n                <td>Driver</td>\n                <td>{{ dbdriver }}</td>\n            </tr>\n            <tr>\n                <td>Version</td>\n                <td>{{ dbversion }}</td>\n            </tr>\n            <tr>\n                <td>Client</td>\n                <td>{{ dbclient }}</td>\n            </tr>\n        </tbody>\n    </table>";
module.exports = {

    ready: function () {
      this.$parent.add(this, '<a title="System Information"><div class="pf-icon-large pf-icon-pagekit"></div></a>', 'system');
    }

  };
;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;

},{}],9:[function(require,module,exports){
var __vue_template__ = "";
module.exports = {

    ready: function () {
        this.$parent.add(this, '<a title="Time"><div class="pf-icon pf-icon-time"></div> {{ duration_str }}</a>');
    }

  };

},{}],10:[function(require,module,exports){
var __vue_template__ = "<div id=\"pk-profiler\" class=\"pf-profiler\">\n\n        <div class=\"pf-navbar\">\n\n            <ul class=\"pf-navbar-nav\" v-repeat=\"navbar\">\n                <li v-html=\"html\" v-on=\"click: open(panel)\"></li>\n            </ul>\n\n            <a class=\"pf-close\" v-on=\"click: close\"></a>\n\n        </div>\n\n        <div v-repeat=\"panels\">\n            <div class=\"pf-profiler-panel\" data-panel=\"{{ $value }}\" v-component=\"{{ $value }}\" v-with=\"data[$value]\"></div>\n        </div>\n\n    </div>";
var $ = window.jQuery, config = window.$debugbar, collectors = {

    system: require('./collectors/system.vue'),
    routes: require('./collectors/routes.vue'),
    events: require('./collectors/events.vue'),
    time: require('./collectors/time.vue'),
    memory: require('./collectors/memory.vue'),
    database: require('./collectors/database.vue'),
    request: require('./collectors/request.vue'),
    auth: require('./collectors/auth.vue')

  };

  module.exports = {

    data: {
        data: {},
        navbar: [],
        panels: []
    },

    created: function () {

        var self = this;

        $.getJSON(config.url, function (data) {

            self.$set('data', data);

            $.each(collectors, function (name) {
                if (data[name]) {
                    self.panels.push(name);
                }
            });

        });

    },

    methods: {

        add: function (collector, navbar, panel) {

            this.navbar.push({panel: panel, html: collector.$interpolate(navbar || '')});

        },

        open: function (panel) {

            if (!panel) {
                return;
            }

            $('[data-panel]', this.$el).each(function () {

                var el = $(this).attr('style', null);

                if (el.data('panel') == panel) {
                    el.css({
                        display: 'block',
                        height: Math.ceil(window.innerHeight / 2)
                    });
                }

            });

        },

        close: function () {

            $('[data-panel]', this.$el).attr('style', null);

        }

    },

    components: collectors

  };
;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;

},{"./collectors/auth.vue":2,"./collectors/database.vue":3,"./collectors/events.vue":4,"./collectors/memory.vue":5,"./collectors/request.vue":6,"./collectors/routes.vue":7,"./collectors/system.vue":8,"./collectors/time.vue":9}]},{},[1]);
