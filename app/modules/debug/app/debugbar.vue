<template>

    <div id="pk-profiler" class="pf-profiler">

        <div class="pf-navbar">

            <ul class="pf-navbar-nav" v-repeat="navbar | orderBy 'priority'">
                <li v-html="html" v-on="click: open(panel)"></li>
            </ul>

            <a class="pf-close" v-on="click: close"></a>

        </div>

        <div v-repeat="panels">
            <div class="pf-profiler-panel" data-panel="{{ $value }}" v-component="{{ $value }}" v-with="data[$value]"></div>
        </div>

    </div>

</template>

<script>

    var $ = require('jquery');
    var Vue = require('vue');
    var config = window.$debugbar;

    module.exports = Vue.extend({

        el: function () {
            return document.createElement('div');
        },

        data: function () {
            return {
                data: {},
                navbar: [],
                panels: []
            }
        },

        created: function () {

            var self = this;

            $.getJSON(config.url, function (data) {

                self.$set('data', data);

                Object.keys(self.$options.components).forEach(function (name) {
                    if (data[name]) {
                        self.panels.push(name);
                    }
                });

            });

        },

        methods: {

            add: function (collector, navbar, options) {

                this.navbar.push($.extend({ html: collector.$interpolate(navbar || '') }, options));

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

        }

    });

    module.exports.register = function (name, options) {
        this.options.components[name] = Vue.extend(options);
    }

</script>
