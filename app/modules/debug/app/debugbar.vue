<template>

    <div id="pk-profiler" class="pf-profiler">

        <div class="pf-navbar">

            <ul class="pf-navbar-nav">
                <li v-repeat="sections | orderBy 'priority'" v-on="click: open(name)">
                    <component is="{{ name }}" data="{{ data[name] }}"></component>
                </li>
            </ul>

            <a class="pf-close" v-on="click: close"></a>

        </div>

        <div class="pf-profiler-panel" v-el="panel" v-style="display: panel ? 'block' : 'none', height: height"></div>

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
                panel: null,
                sections: [],
                instances: {}
            }
        },

        created: function () {

            this.$http.get(config.url, function (data) {

                this.$set('data', data);

                var sections = this.sections;

                $.each(this.$options.components, function (name, component) {
                    if (data[name]) {
                        sections.push($.extend({name: name}, component.options.section));
                    }
                });

            });

        },

        computed: {

            height: function () {
                return Math.ceil(window.innerHeight / 2) + 'px';
            }

        },

        methods: {

            add: function (instance) {
                this.instances[instance.$options.name] = instance;
            },

            open: function (name) {

                var instance = this.instances[name], el = document.createElement('div'), template = instance.$options.section.panel, panel;

                if (template) {

                    if (this.panel) {
                        this.panel.$destroy(true);
                    }

                    panel = instance.$addChild({el: el, template: template, inherit: true});
                    panel.$appendTo(this.$$.panel);

                    this.$set('panel', panel);
                }
            },

            close: function () {

                if (this.panel) {
                    this.panel.$destroy(true);
                }

                this.$set('panel', null);
            }

        }

    });

</script>
