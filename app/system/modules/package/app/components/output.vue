<template>

    <div>
        <v-modal v-ref="output" options="{{ options }}" large>
            <div v-html="output"></div>
        </v-modal>
    </div>

</template>

<script>

    module.exports = {
        data: function () {
            return {
                output: '',
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
            setOutput: function (output) {
                this.output = output;
            },

            open: function () {
                this.$.output.open();
            },

            close: function () {
                this.$.output.close();
                this.$destroy();
            }
        }
    };

</script>
