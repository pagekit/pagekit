<template>

    <div class="uk-panel-badge">
        <ul class="uk-subnav pk-subnav-icon">
            <li v-show="!editing">
                <a class="pk-icon-contrast pk-icon-edit pk-icon-hover uk-hidden" :title="'Edit' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="$parent.edit"></a>
            </li>
            <li v-show="!editing">
                <a class="pk-icon-contrast pk-icon-handle pk-icon-hover uk-hidden uk-sortable-handle" :title="'Drag' | trans" data-uk-tooltip="{delay: 500}"></a>
            </li>
            <li v-show="editing">
                <a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="$parent.remove" v-confirm="'Delete widget?'"></a>
            </li>
            <li v-show="editing">
                <a class="pk-icon-check pk-icon-hover" :title="'Close' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="$parent.save"></a>
            </li>
        </ul>
    </div>

    <form class="pk-panel-teaser uk-form uk-form-stacked" v-show="editing" @submit.prevent>

        <div class="uk-form-row">
            <label for="form-city" class="uk-form-label">{{ 'Location' | trans }}</label>

            <div class="uk-form-controls">
                <div v-el:autocomplete class="uk-autocomplete uk-width-1-1">
                    <input id="form-city" class="uk-width-1-1" type="text" :placeholder="location" v-el:location @blur="clear" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Unit' | trans }}</span>

            <div class="uk-form-controls uk-form-controls-text">
                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="metric" v-model="widget.units"> {{ 'Metric' | trans }}</label>
                </p>

                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="imperial" v-model="widget.units"> {{ 'Imperial' | trans }}</label>
                </p>
            </div>
        </div>

    </form>

    <div class="pk-panel-background uk-contrast" v-if="status != 'loading'">
        <h1 class="uk-margin-large-top uk-margin-small-bottom uk-text-center pk-text-xlarge" v-if="time">{{ time | date format }}</h1>

        <h2 class="uk-text-center uk-h4 uk-margin-remove" v-if="time">{{ time | date 'longDate' }}</h2>
        <div class="uk-margin-large-top uk-flex uk-flex-middle uk-flex-space-between uk-flex-wrap" data-uk-margin>
            <h3 class="uk-margin-remove" v-if="widget.city">{{ widget.city }}</h3>
            <h3 class="uk-flex uk-flex-middle uk-margin-remove" v-if="status=='done'">{{ temperature }} <img class="uk-margin-small-left" :src="icon" width="25" height="25" alt="Weather"></h3>
        </div>
    </div>

    <div class="uk-text-center" v-else>
        <v-loader></v-loader>
    </div>

</template>

<script>

    module.exports = {

        type: {

            id: 'location',
            label: 'Location',
            disableToolbar: true,
            description: function () {
            },
            defaults: {
                units: 'metric'
            }

        },

        replace: false,

        props: ['widget', 'editing'],

        data: function () {
            return {
                status: '',
                timezone: {},
                icon: '',
                temp: 0,
                time: 0,
                format: 'shortTime'
            };
        },

        ready: function () {

            var vm = this, list;

            UIkit
                .autocomplete(this.$els.autocomplete, {

                    source: function (release) {

                        vm.$http.get('admin/dashboard/weather', {action: 'find', data: {q: this.input.val(), type: 'like'}}).then(
                            function (res) {

                                var data = res.data;
                                list = data.list || [];
                                release(list);

                            },
                            function () {
                                release([]);
                            });

                    },

                    template: '<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">\
                                  {{~items}}<li data-id="{{$item.id}}"><a>{{$item.name}} <span>, {{$item.sys.country}}</span></a></li>{{/items}}\
                                  {{^items.length}}<li class="uk-skip"><a class="uk-text-muted">{{msgNoResults}}</a></li>{{/end}} \
                               </ul>',

                    renderer: function (data) {

                        this.dropdown.append(this.template({items: data || [], msgNoResults: vm.$trans('No location found.')}));
                        this.show();
                    }

                })
                .on('selectitem.uk.autocomplete', function (e, data) {

                    var location = _.find(list, 'id', data.id);

                    Vue.nextTick(function () {
                        vm.$els.location.blur();
                    });

                    if (!location) {
                        return;
                    }

                    vm.$set('widget.uid', location.id);
                    vm.$set('widget.city', location.name);
                    vm.$set('widget.country', location.sys.country);
                    vm.$set('widget.coords', location.coord);
                });

            this.timer = setInterval(this.updateClock(), 60 * 1000);
        },

        watch: {

            'widget.uid': {

                handler: function (uid) {

                    if (uid === undefined) {
                        this.$set('widget.uid', '');
                        this.$parent.save();
                        this.$parent.edit(true);
                    }

                    if (!uid) return;

                    this.load();

                },
                immediate: true

            },

            'timezone': 'updateClock'

        },

        computed: {

            location: function () {
                return this.widget.city ? this.widget.city + ', ' + this.widget.country : '';
            },

            temperature: function () {

                if (this.widget.units !== 'imperial') {
                    return Math.round(this.temp) + ' °C';
                }

                return Math.round(this.temp * (9 / 5) + 32) + ' °F';
            }

        },

        methods: {

            load: function () {

                if (!this.widget.uid) {
                    return;
                }

                this.$http.get('admin/dashboard/weather', {action: 'weather', data: {id: this.widget.uid, units: 'metric'}}, {cache: 60}).then(
                    function (res) {
                        var data = res.data;
                        if (data.cod == 200) {
                            this.init(data)
                        } else {
                            this.$set('status', 'error');
                        }

                    },
                    function () {
                        this.$set('status', 'error');
                    }
                );

                this.$http.get('https://maps.googleapis.com/maps/api/timezone/json',
                    {location: this.widget.coords.lat + ',' + this.widget.coords.lon, timestamp: Math.floor(Date.now() / 1000)},
                    {cache: {key: 'timezone-' + this.widget.coords.lat + this.widget.coords.lon, lifetime: 1440}}).then(function (res) {

                    var data = res.data;
                    data.offset = data.rawOffset + data.dstOffset;

                    this.$set('timezone', data);

                }, function () {
                    this.$set('status', 'error');
                });


            },

            init: function (data) {

                this.$set('temp', data.main.temp);
                this.$set('icon', this.getIconUrl(data.weather[0].icon));
                this.$set('status', 'done');

            },

            getIconUrl: function (icon) {

                var icons = {

                    '01d': 'sun.svg',
                    '01n': 'moon.svg',
                    '02d': 'cloud-sun.svg',
                    '02n': 'cloud-moon.svg',
                    '03d': 'cloud.svg',
                    '03n': 'cloud.svg',
                    '04d': 'cloud.svg',
                    '04n': 'cloud.svg',
                    '09d': 'drizzle-sun.svg',
                    '09n': 'drizzle-moon.svg',
                    '10d': 'rain-sun.svg',
                    '10n': 'rain-moon.svg',
                    '11d': 'lightning.svg',
                    '11n': 'lightning.svg',
                    '13d': 'snow.svg',
                    '13n': 'snow.svg',
                    '50d': 'fog.svg',
                    '50n': 'fog.svg'

                };

                return this.$url('app/system/modules/dashboard/assets/images/weather-{icon}', {icon: icons[icon]});
            },

            updateClock: function () {

                var offset = this.$get('timezone.offset') || 0,
                    date = new Date(),
                    time = offset ? new Date(date.getTime() + date.getTimezoneOffset() * 60000 + offset * 1000) : new Date();

                this.$set('time', time);

                return this.updateClock;
            },

            clear: function () {
                this.$els.location.value = '';
            }

        },

        destroyed: function () {

            clearInterval(this.timer);

        }

    }

</script>
