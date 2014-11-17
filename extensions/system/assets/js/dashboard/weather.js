require(['jquery', 'uikit', 'uikit!autocomplete','domReady!'], function($, uikit) {

    var api = 'http://api.openweathermap.org/data/2.5', storage = sessionStorage || {};

    // widget init

    $('[data-weather]').each(function() {

        var widget = $(this), config = widget.data('weather');

        loadData(config).then(function(data, status, jqxhr) {

            if (data.cod != 200) {
                return $.Deferred().reject(data);
            }

            return $.Deferred().resolve(data, status, jqxhr);

        }).done(function(data) {

            var location = config.location.split(',');

            $('.js-weather-city', widget).html(location[0]);
            $('.js-weather-country', widget).html(location[1]);
            $('.js-weather-temperature', widget).html(Math.round(data.main.temp) + (config.units == 'metric' ? ' &deg;C' : ' &deg;F'));
            $('.js-weather-icon', widget).attr('src', getIconUrl(data.weather[0].icon));

            widget.find('.js-spinner').addClass('uk-hidden');

        }).fail(function() {
            widget.replaceWith(($('.js-error', widget)).removeClass('uk-hidden'));
        });

    });

    function loadData(config) {

        var key = 'weather-' + config.id + config.units[0], cache = storage[key];

        if (cache) {
            return $.Deferred().resolve(JSON.parse(cache)).promise();
        }

        return $.getJSON(api + '/weather?callback=?', { id: config.id, units: config.units }, function(data) {
            if (data.cod == 200) {
                storage[key] = JSON.stringify(data);
            }
        });
    }

    function getIconUrl(icon) {

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

        return require.toUrl('extensions/system/assets/images/weather-' + icons[icon]);
    }

    // widget settings

    var autocomplete = uikit.autocomplete($('#form-weather-location').parent(), {

        'source': function(release) {

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

    autocomplete.element.on('uk.autocomplete.select', function(e, data){
        autocomplete.input.next().val(data.id);
    });

});
