<template>

    <div class="uk-grid uk-grid-small" data-uk-grid-margin>
        <div class="uk-width-large-1-2">
            <div class="uk-form-icon uk-display-block">
                <i class="pk-icon-calendar pk-icon-muted"></i>
                <input class="uk-width-1-1" type="text" v-el:datepicker v-model="date" v-validate:required="isRequired" lazy>
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="uk-form-icon uk-display-block" v-el:timepicker>
                <i class="pk-icon-time pk-icon-muted"></i>
                <input class="uk-width-1-1" type="text" v-model="time" v-validate:required="isRequired" lazy>
            </div>
        </div>
    </div>

</template>

<script>

    module.exports = {

        props: ['datetime', 'required'],

        ready: function () {
            UIkit.datepicker(this.$els.datepicker, {format: this.dateFormat, pos: 'bottom'});
            UIkit.timepicker(this.$els.timepicker, {format: this.clockFormat});
        },

        computed: {

            dateFormat: function () {
                return window.$locale.DATETIME_FORMATS.shortDate
                    .replace(/\bd\b/i, 'DD')
                    .replace(/\bm\b/i, 'MM')
                    .replace(/\by\b/i, 'YYYY')
                    .toUpperCase();
            },

            timeFormat: function () {
                return window.$locale.DATETIME_FORMATS.shortTime.replace(/\bh\b/i, 'hh');
            },

            clockFormat: function () {
                return this.timeFormat.match(/a/) ? '12h' : '24h';
            },

            date: {

                get: function () {
                    return UIkit.Utils.moment(this.datetime).format(this.dateFormat);
                },

                set: function (date) {
                    var prev = new Date(this.datetime);
                    date = UIkit.Utils.moment(date, this.dateFormat);
                    date.hours(prev.getHours());
                    date.minutes(prev.getMinutes());
                    this.$set('datetime', date.utc().format());
                }

            },

            time: {

                get: function () {
                    return UIkit.Utils.moment(this.datetime).format(this.timeFormat);
                },

                set: function (time) {
                    var date = new Date(this.datetime);
                    time = UIkit.Utils.moment(time, this.timeFormat);
                    date.setHours(time.hours(), time.minutes());
                    this.$set('datetime', date.toISOString());
                }

            },

            isRequired: function () {
                return this.required !== undefined;
            }

        }

    };

    Vue.component('input-date', function (resolve, reject) {
        Vue.asset({
            js: [
                'app/assets/uikit/js/components/autocomplete.min.js',
                'app/assets/uikit/js/components/datepicker.min.js',
                'app/assets/uikit/js/components/timepicker.min.js'
            ]
        }).then(function () {
            resolve(module.exports);
        })
    });

</script>
