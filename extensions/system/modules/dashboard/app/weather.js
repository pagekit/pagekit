jQuery(function($) {

    var api = 'http://api.openweathermap.org/data/2.5';

    // init

    new Vue({

        el: '[data-weather]',

        data: {
            status: 'loading',
            storage: sessionStorage || {}
        },

        ready: function() {

            var self = this, config = $(this.$el).data('weather'), location = config.location.split(',');

            self.loadData(config).then(function(data, status, jqxhr) {

                if (data.cod != 200) {
                    return $.Deferred().reject(data);
                }

                return $.Deferred().resolve(data, status, jqxhr);

            }).done(function(data) {

                self.$add('city', location[0]);
                self.$add('country', location[1]);
                self.$add('temperature', Math.round(data.main.temp) + (config.units == 'metric' ? ' °C' : ' °F'));
                self.$add('icon', self.getIconUrl(data.weather[0].icon));
                self.$set('status', 'done');

            }).fail(function() {

                self.$set('status', 'error');

            });

        },

        methods: {

            loadData: function(config) {

                var self = this, key = 'weather-' + config.id + config.units[0], cache = self.storage[key];

                if (cache) {
                    return $.Deferred().resolve(JSON.parse(cache)).promise();
                }

                return $.getJSON(api + '/weather?callback=?', { id: config.id, units: config.units }, function(data) {
                    if (data.cod == 200) {
                        self.storage[key] = JSON.stringify(data);
                    }
                });
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

                return System.url('extensions/system/assets/images/weather-:icon', {icon: icons[icon]}, true);
            }

        }

    });

    // settings

    var location = $('#form-weather-location'), autocomplete;

    if (location.length) {

        autocomplete = UIkit.autocomplete(location.parent(), {

            source: function(release) {
                $.getJSON(api + '/find?callback=?', { q: this.input.val(), type: 'like' }, function(data) {
                    if (data.cod == 200) {
                        release(data.list);
                    } else {
                        release([]);
                    }
                });
            },

            template: '<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">{{~items}}<li data-value="{{$item.name}}" data-id="{{$item.id}}"><a>{{$item.name}}</a></li>{{/items}}</ul>'

        });

        autocomplete.element.on('autocomplete-select', function(e, data){
            autocomplete.input.next().val(data.id);
        });
    }

});
