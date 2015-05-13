<template>

    <div class="uk-form-row">
        <label for="form-weather-location" class="uk-form-label">{{ 'Location' | trans }}</label>
        <div class="uk-form-controls">
            <div v-el="location" class="uk-autocomplete">
                <input id="form-weather-location" class="uk-form-width-large" type="text" name="widget[location]" v-model="widget.settings.location" autocomplete="off">
            </div>
            <p class="uk-form-help-block">{{ 'Enter the name of the city you want to get weather information for.' | trans }}</p>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Unit' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="radio" name="widget[units]" value="metric" v-model="widget.settings.units"> {{ 'Metric' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" name="widget[units]" value="imperial" v-model="widget.settings.units"> {{ 'Imperial' | trans }}</label>
            </p>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            name: 'weather',
            priority: 0,
            active: 'dashboard.weather'
        },

        template: __vue_template__,

        ready: function() {

            var self = this, api = 'http://api.openweathermap.org/data/2.5';

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

            }).element.on('select.uk.autocomplete', function(e, data) {
                    self.$set('widget.settings.id', data.id);
                });

        }

    }

</script>
