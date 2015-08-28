<template>

    <div>
        <v-modal v-ref="output" options="{{ options }}" large>
            <h1>{{ title }}</h1>

            <pre v-html="output"></pre>

            <v-loader v-show="status == 'loading'"></v-loader>

            <a class="uk-button uk-button-success" v-show="status == 'success'" v-on="click: close">{{ 'Ok' | trans }}</a>
            <a class="uk-button uk-button-error" v-show="status == 'error'" v-on="click: close">{{ 'Failed' | trans }}</a>

        </v-modal>
    </div>

</template>

<script>

    module.exports = {

        data: function () {
            return {
                title: '',
                output: '',
                status: 'loading',
                cb: null,
                options: {
                    bgclose: false,
                    keyboard: false
                }
            }
        },

        created: function () {
            this.$mount().$appendTo('body');
        },

        methods: {
            init: function (request, title) {
                var vm = this;
                this.title = title;

                request.onprogress = function () {
                    vm.setOutput(this.responseText);
                };

                this.open();
            },

            onClose: function (cb) {
                this.cb = cb;
            },

            setOutput: function (output) {
                var lines = output.split("\n");
                var match = lines[lines.length - 1].match(/^status=(success|error)$/);

                if (match) {
                    this.status = match[1];
                    delete lines[lines.length - 1];
                    this.output = lines.join("\n");
                } else {
                    this.output = output;
                }

            },

            open: function () {
                this.$.output.open();
            },

            close: function () {
                if (this.cb) {
                    this.cb(this);
                }

                this.$.output.close();
                this.$destroy();
            }
        }
    };

</script>
