<template>

    <div id="pk-profiler" class="pf-profiler">

        <div class="pf-navbar">

            <ul class="pf-navbar-nav">
                <li v-for="section in sections | orderBy 'priority'" @click="open(section.name)">
                    <component :is="section.name" :data="data[section.name]"></component>
                </li>
            </ul>

            <a class="pf-close" @click.prevent="close"></a>

        </div>

        <div class="pf-profiler-panel" v-el:panel :style="{display: panel ? 'block' : 'none', height: height}"></div>

    </div>

</template>

<script>

    var _ = require('lodash');
    var config = window.$debugbar;

    module.exports = {

        data: function () {
            return {
                data: {},
                panel: null,
                sections: {}
            }
        },

        created: function () {

            this.$http.get(config.url).then(function (res) {
                var data = res.data;
                this.$set('data', data);

                _.forIn(this.$options.components, function (component, name) {
                    if (data[name]) {
                        Vue.set(this.sections, name, _.merge({name: name}, component.options.section));
                    }
                }, this);

            });

        },

        computed: {

            height: function () {
                return Math.ceil(window.innerHeight / 2) + 'px';
            }

        },

        methods: {

            open: function (name) {

                var section = this.sections[name], panel, vm = _.find(this.$children, '$options.name', name);

                if (!section.panel) {
                    return;
                }

                if (this.panel) {
                    this.close();
                }

                panel = new Vue({
                    parent: vm,
                    template: section.panel,
                    data: this.data[section.name],
                    filters: vm.$options.filters.__proto__
                });
                panel.$mount().$appendTo(this.$els.panel);

                this.$set('panel', panel);
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
