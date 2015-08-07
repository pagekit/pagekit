<template>

    <div class="uk-grid uk-grid-small" data-uk-grid-margin>
        <div class="uk-width-large-1-2">
            <div class="uk-form-icon uk-display-block">
                <i class="pk-icon-calendar pk-icon-muted"></i>
                <input class="uk-width-1-1" type="text" v-el="datepicker" v-model="date" v-valid="required: isRequired" lazy>
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="uk-form-icon uk-display-block" v-el="timepicker">
                <i class="pk-icon-time pk-icon-muted"></i>
                <input class="uk-width-1-1" type="text" v-model="time" v-valid="required: isRequired" lazy>
            </div>
        </div>
    </div>

</template>

<script>

    module.exports = {

        props: ['datetime', 'required'],

        ready: function () {
            UIkit.datepicker(this.$$.datepicker, {format: 'YYYY-MM-DD', pos: 'bottom'});
            UIkit.timepicker(this.$$.timepicker, {format: this.$date('1970-01-01T12:00:00+0000', {time: 'short'}).match(/pm/i) ? '12h' : '24h'});
        },

        computed: {

            date: {

                get: function () {
                    return this.$date(this.datetime, 'short');
                },

                set: function (date) {
                    var prev = new Date(this.datetime);
                    date = new Date(date);
                    date.setHours(prev.getHours(), prev.getMinutes());
                    this.$set('datetime', date.toISOString());
                }

            },

            time: {

                get: function () {

                    var t = this.$date(this.datetime, {time: 'short'}),
                        h = t.split(':')[0];

                    if (h.length == 1) {
                        t = '0' + t;
                    }

                    return t;
                },

                set: function (time) {
                    var parsed = this.parseTime(time), date = new Date(this.datetime);
                    date.setHours(parsed[0], parsed[1]);
                    this.$set('datetime', date.toISOString());
                }

            },

            isRequired: function () {
                return this.required !== undefined;
            }

        },

        methods: {

            parseTime: function (time) {

                // Convert a string like 11:30 PM to 24h format
                var matches = time.match(/(\d+):(\d+)\s?(\w)?/),
                    hours = Number(matches[1]),
                    minutes = Number(matches[2]),
                    meridian = matches[3] && matches[3].toLowerCase();

                if (meridian == 'p' && hours < 12) {
                    hours = hours + 12;
                } else if (meridian == 'a' && hours == 12) {
                    hours = hours - 12;
                }

                return [hours, minutes];

            }

        }

    };

    Vue.component('input-date', function (resolve, reject) {
        Vue.asset({
            js: [
                'vendor/assets/uikit/js/components/autocomplete.min.js',
                'vendor/assets/uikit/js/components/datepicker.min.js',
                'vendor/assets/uikit/js/components/timepicker.min.js'
            ]
        }, function () {
            resolve(module.exports);
        })
    });

</script>
