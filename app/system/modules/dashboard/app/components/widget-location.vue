<template>

    <form class="pk-panel-teaser uk-form uk-form-stacked" v-show="editing" v-on="submit: $event.preventDefault()">

        <h3 class="uk-panel-title">{{ 'Location Widget' | trans }}</h3>

        <div class="uk-form-row">
            <label for="form-city" class="uk-form-label">{{ 'Location' | trans }}</label>
            <div class="uk-form-controls">
                <div v-el="location" class="uk-autocomplete uk-width-1-1">
                    <input id="form-city" class="uk-width-1-1" type="text" v-model="widget.city" autocomplete="off">
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

    <div class="uk-text-center">

        <div v-show="status == 'loading'"><i class="uk-icon-medium uk-icon-spinner uk-icon-spin"></i></div>
        <div class="uk-alert uk-alert-danger" v-show="status == 'error'">{{ 'Unable to retrieve weather data.' | trans }}</div>
        <div class="uk-alert uk-alert-warning" v-show="!widget.uid && !editing">{{ 'No location given.' | trans }}</div>

        <div v-show="status != 'error'">
            <div class="pk-weather-image">
                <img class="uk-text-top" v-attr="src: icon">
                <span class="uk-text-large uk-text-muted pk-weather-temperature">{{ temperature }}</span>
            </div>
            <h1 class="uk-h2 uk-margin-remove">{{ widget.city }}</h1>
            <h1 v-if="time" class="uk-h2 uk-margin-remove">{{ time | date format }}</h1>
            <h2 class="uk-h3 uk-margin-remove uk-text-muted">{{ timezone.id }}</h2>
            <h2 class="uk-h3 uk-margin-remove uk-text-muted">{{ timezone.name }}</h2>

        </div>

    </div>

</template>

<script>

    var $ = require('jquery');
    var api = '//api.openweathermap.org/data/2.5';
    var storage = sessionStorage || {};

    module.exports = {

        type: {

            id: 'location',
            label: 'Location',
            description: function () {

            },
            defaults: {
                units: 'metric'
            }

        },

        data: function() {
            return {
                format: {time: 'medium'}
            };
        },

        ready: function() {

            var vm = this, list;

            UIkit
                .autocomplete(this.$$.location, {

                    source: function(release) {

                        vm.$http.jsonp(api + '/find', {q: this.input.val(), type: 'like'}, function(data) {

                            list = data.list || [];
                            release(list);

                        }).error(function() {
                            release([]);
                        });

                    },

                    template: '<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">{{~items}}<li data-value="{{$item.name}}" data-id="{{$item.id}}"><a>{{$item.name}}</a></li>{{/items}}</ul>'

                })
                .on('selectitem.uk.autocomplete', function(e, data) {

                    var location = _.find(list, {id: data.id});

                    if (!location) {
                        return;
                    }

                    vm.$set('widget.uid', location.id);
                    vm.$set('widget.city', location.name);
                    vm.$set('widget.country', location.sys.country);
                    vm.$set('widget.coords', location.coord);
                });

            this.$watch('widget.uid', function(uid) {

                if (!uid) {
                    this.$parent.edit(true);
                }

                this.loadWeather();
                this.loadTime();

            }, false, true);

            this.timer = setInterval(this.updateClock, 1000);
        },

        watch: {

            'widget.units': 'loadWeather',
            'widget.coords': 'loadTime'

        },

        methods: {

            loadWeather: function() {

                if (!this.widget.uid || !this.widget.units) {
                    return;
                }

                var key = 'weather-' + this.widget.uid + this.widget.units;

                if (storage[key]) {

                    this.init(JSON.parse(storage[key]));

                } else {

                    this.$http.jsonp(api + '/weather?callback=?', { id: this.widget.uid, units: this.widget.units }, function(data) {

                        if (data.cod == 200) {
                            storage[key] = JSON.stringify(data);
                            this.init(data)
                        } else {
                            this.$set('status', 'error');
                        }

                    }).error(function() {
                        this.$set('status', 'error');
                    });

                }

            },

            loadTime: function() {

                if (!this.widget.coords) {
                    return;
                }

                var key = 'timezone-' + this.widget.coords.lat + this.widget.coords.lon;

                if (storage[key]) {

                    this.$set('timezone', JSON.parse(storage[key]));

                } else {

                    this.$http.get(this.$url('admin/system/intl/timezone', {lat: this.widget.coords.lat, lon: this.widget.coords.lon}), function(data) {

                        storage[key] = JSON.stringify(data);
                        this.$set('timezone', data);

                    }).error(function() {
                        this.$set('status', 'error');
                    });

                }
            },

            init: function(data) {

                this.$set('temperature', Math.round(data.main.temp) + (this.widget.units === 'metric' ? ' °C' : ' °F'));
                this.$set('icon', this.getIconUrl(data.weather[0].icon));
                this.$set('status', 'done');

            },

            getIconUrl: function(icon) {

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

                return this.$url.static('app/system/modules/dashboard/assets/images/weather-:icon', {icon: icons[icon]});
            },

            updateClock: function() {

                var offset = this.$get('timezone.offset'), date = new Date();

                this.$set('time', offset ? new Date(date.getTime() + date.getTimezoneOffset() * 60000 + offset * 1000) : false);

            }

        },

        destroyed: function() {

            clearInterval(this.timer);

        }

    }

</script>
