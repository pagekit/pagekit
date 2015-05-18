<template>

    <form class="uk-form uk-margin" v-show="editing" v-on="submit: $event.preventDefault()">

        <div class="uk-form-row">
            <label for="form-weather-location" class="uk-form-label">{{ 'Location' | trans }}</label>
            <div class="uk-form-controls">
                <div v-el="location" class="uk-autocomplete">
                    <input id="form-weather-location" class="uk-form-width-large" type="text" v-model="widget.location" autocomplete="off">
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

        <div class="pk-weather-image">
            <img class="uk-text-top" v-attr="src: icon">
            <span class="uk-text-large uk-text-muted pk-weather-temperature">{{ temperature }}</span>
        </div>
        <h1 class="uk-h2 uk-margin-remove">{{ city }}</h1>
        <h2 class="uk-h3 uk-margin-remove uk-text-muted">{{ country }}</h2>

    </div>

</template>

<script>

    var $ = require('jquery');
    var api = '//api.openweathermap.org/data/2.5';
    var storage = sessionStorage || {};

    module.exports = {

        type: {

            id: 'weather',
            label: 'Weather',
            description: function () {

            },
            defaults: {
                units: 'metric'
            }

        },

        ready: function() {

            var self = this;

            UIkit.autocomplete(this.$$.location, {

                source: function(release) {
                    $.getJSON(api + '/find?callback=?', {q: this.input.val(), type: 'like'}, function(data) {
                        if (data.cod == 200) {
                            release(data.list);
                        } else {
                            release([]);
                        }
                    });
                },

                template: '<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">{{~items}}<li data-value="{{$item.name}}" data-id="{{$item.id}}"><a>{{$item.name}}</a></li>{{/items}}</ul>'

            })
            .element.on('select.uk.autocomplete', function(e, data) {

                if (!data) {
                    return;
                }

                self.$set('widget.uid', data.id);
                self.$set('widget.location', data.value);
            });

            this.$watch('widget.uid', function(uid) {

                if (!uid) {
                    this.$parent.edit(true);
                }

                this.load();

            }, false, true);

            this.$watch('widget.units', this.load, false, false);
        },

        methods: {

            load: function() {

                if (!this.widget.uid || !this.widget.units) {
                    return;
                }

                var self = this,
                    key = 'weather-' + this.widget.uid + this.widget.units,
                    cache = storage[key];

                if (cache) {

                    this.init(JSON.parse(cache));

                } else {

                    $.getJSON(api + '/weather?callback=?', { id: this.widget.uid, units: this.widget.units }, function(data) {

                        if (data.cod == 200) {
                            storage[key] = JSON.stringify(data);
                            self.init(data)
                        } else {
                            self.$set('status', 'error');
                        }

                    }).fail(function() {

                        self.$set('status', 'error');

                    });

                }

            },

            init: function(data) {

                var location = (this.widget.location || '').split(',');

                this.$set('city', location[0]);
                this.$set('country', location[1]);
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
            }

        }

    }

</script>
