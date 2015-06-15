<template>

    <div id="pk-profiler" class="pf-profiler">

        <div class="pf-navbar">

            <ul class="pf-navbar-nav" v-repeat="navbar | orderBy 'priority'">
                <li v-html="html" v-on="click: open(panel)"></li>
            </ul>

            <a class="pf-close" v-on="click: close"></a>

        </div>

        <div v-repeat="panels">
            <component class="pf-profiler-panel" v-style="display: $value === panel ? 'block' : 'none', height: height" is="{{ $value }}" data="{{ data[$value] }}"></component>
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

            this.$http.get(config.url, function (data) {

                this.$set('data', data);

                var panels = this.panels;

                $.each(this.$options.components, function (name) {
                    if (data[name]) {
                        panels.push(name);
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

</script>
