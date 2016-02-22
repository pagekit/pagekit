module.exports = {

    data: function () {
        return {
            pkg: {},
            output: '',
            status: 'loading',
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

        init: function () {
            var vm = this;

            this.open();
            return {
                onprogress: function () {
                    vm.setOutput(this.responseText);
                }
            }

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
            this.$refs.output.open();
            this.$refs.output.modal.on('hide.uk.modal', this.onClose);
        },

        close: function () {
            this.$refs.output.close();
        },

        onClose: function () {
            if (this.cb) {
                this.cb(this);
            }

            this.$destroy();
        }

    },

    watch: {
        status: function () {
            if (this.status !== 'loading') {
                this.$refs.output.modal.options.bgclose = true;
                this.$refs.output.modal.options.keyboard = true;
            }
        }
    }

};
