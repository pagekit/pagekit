<template>

    <div>
        <v-modal v-ref="output" options="{{ options }}">

            <div class="uk-modal-header uk-flex uk-flex-middle">
                <h2>{{ title }}</h2>
            </div>

            <pre class="pk-pre uk-text-break" v-html="output"></pre>

            <v-loader v-show="status == 'loading'"></v-loader>

            <div class="uk-modal-footer uk-text-right" v-show="status != 'loading'">
                <a class="uk-button uk-button-link" v-on="click: close">{{ 'Close' | trans }}</a>
                <a class="uk-button uk-button-primary" v-repeat="buttons" v-on="click: close(action)">{{ title }}</a>
            </div>

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
                buttons: [],
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

            addButton: function (title, action) {
                this.buttons.push({title: title, action: action});
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

            close: function (action) {
                if (typeof action == 'function') {
                    action();
                }

                if (this.cb) {
                    this.cb(this);
                }

                this.$.output.close();
                this.$destroy();
            }
        }
    };

</script>
