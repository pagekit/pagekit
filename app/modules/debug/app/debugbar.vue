<template>

    <div id="pk-profiler" class="pf-profiler">

        <div class="pf-navbar">

            <ul class="pf-navbar-nav" v-repeat="navbar | orderBy 'priority'">
                <li v-html="html" v-on="click: open(panel)"></li>
            </ul>

            <a class="pf-close" v-on="click: close"></a>

        </div>

        <div v-repeat="panels">
            <div class="pf-profiler-panel" v-style="
                display: $value === panel ? 'block' : 'none',
                height: height
            " v-component="{{ $value }}" v-with="data[$value]"></div>
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
                panels: [],
                panel: null
            }
        },

        created: function () {

            var self = this;

            $.getJSON(config.url, function (data) {

                self.$set('data', data);

                $.each(self.$options.components, function (name) {
                    if (data[name]) {
                        self.panels.push(name);
                    }
                });

            });

        },

        computed: {

            height: function() {
                return Math.ceil(window.innerHeight / 2) + 'px';
            }

        },

        methods: {

            add: function (collector, navbar, options) {

                this.navbar.push($.extend({ html: collector.$interpolate(navbar || '') }, options));

            },

            open: function (panel) {

                if (panel) {
                    this.$set('panel', panel);
                }

            },

            close: function () {

                this.$set('panel', null);

            }

        }

    });

    module.exports.register = function (name, options) {
        this.options.components[name] = Vue.extend(options);
    };

</script>
