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

        init: function (request) {
            var vm = this;

            request.onprogress = function () {
                vm.setOutput(this.responseText);
            };

            this.open();
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
            this.$.output.modal.on('hide.uk.modal', this.onClose);
        },

        close: function () {
            this.$.output.close();
        },

        onClose: function () {
            if (this.cb) {
                cb(this);
            }

            this.$destroy();
        }

    },

    watch: {
        status: function () {
            if (this.status !== 'loading') {
                this.$.output.modal.options.bgclose = true;
                this.$.output.modal.options.keyboard = true;
            }
        }
    }

};