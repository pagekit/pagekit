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
    var config = window.$debugbar;

    module.exports = {

        el: function () {
            return document.createElement('div');
        },

        data: function () {
            return {
                data: {},
                panel: null,
                sections: {}
            }
        },

        created: function () {

            this.$http.get(config.url, function (data) {

                this.$set('data', data);

                var sections = this.sections;

                $.each(this.$options.components, function (name, component) {
                    if (data[name]) {
                        sections.$add(name, $.extend({name: name}, component.options.section));
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

            add: function (vm) {
                this.sections[vm.$options.name]['vm'] = vm;
            },

            open: function (name) {

                var section = this.sections[name], el = document.createElement('div'), panel;

                if (section.panel) {

                    if (this.panel) {
                        this.panel.$destroy(true);
                    }

                    panel = section.vm.$addChild({el: el, template: section.panel, inherit: true});
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

    };

</script>
