module.exports = function (_) {

    return {

        props: ['config', 'values'],

        template: '<partial name="{{ type }}"></partial>',

        data: function () {
            return _.extend({
                key: '',
                name: '',
                type: 'text',
                label: '',
                attrs: {},
                options: [],
            }, this.config);
        },

        created: function () {
            this.$set('key', '["' + this.name.replace('.', '"]["') + '"]');
            this.$options.partials = _.field.types;
        },

        computed: {

            value: {

                get: function () {
                    return this.$get('values' + this.key);
                },

                set: function (value) {
                    this.$set('values' + this.key, value);
                }

            }

        },

        methods: {

            filterOptions: function (options) {

                var opts = [];

                if (!options) {
                    _.warn('Invalid options provided for ' + this.name);
                    return opts;
                }

                _.each(options, function (value, name) {
                    if (_.isObject(value)) {
                        opts.push({label: name, options: this.filterOptions(value)});
                    } else {
                        opts.push({text: name, value: value});
                    }
                }, this);

                return opts;
            }

        },

        filters: {

            options: function (options) {
                return this.filterOptions(options);
            }

        }

    };

};
