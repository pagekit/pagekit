<template>

    <div id="pk-profiler" class="pf-profiler">

        <div class="pf-navbar">

            <ul v-if="data" class="pf-navbar-nav">
                <li v-for="section in sections | orderBy 'priority'" :is="section.name" :data="data[section.name]" @click="open(section.name)"></li>
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
                request: null,
                data: null,
                panel: null,
                sections: {}
            }
        },

        created: function () {

            _.forIn(this.$options.components, function (component, name) {

                if (component.options && component.options.section) {
                    Vue.set(this.sections, name, _.merge({name: name}, component.options.section));
                }

            }, this);

            this.load(config.current).then(function (res) {
                this.$set('request', res.data.__meta);
            });
        },

        computed: {

            height: function () {
                return Math.ceil(window.innerHeight / 2) + 'px';
            }

        },

        methods: {

            load: function (id) {
                return this.$http.get('_debugbar/{id}', {id: id}).then(function (res) {
                    this.$set('data', res.data);
                    return res;
                });
            },

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
