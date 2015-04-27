<template>

    <div id="pk-profiler" class="pf-profiler">

        <div class="pf-navbar">

            <ul class="pf-navbar-nav" v-repeat="navbar">
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

  var $ = require('jquery'), config = window.$debugbar, collectors = {

    system: require('./components/system.vue'),
    routes: require('./components/routes.vue'),
    events: require('./components/events.vue'),
    time: require('./components/time.vue'),
    memory: require('./components/memory.vue'),
    database: require('./components/database.vue'),
    request: require('./components/request.vue'),
    auth: require('./components/auth.vue')

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

</script>