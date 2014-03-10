require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    var api = 'http://api.openweathermap.org/data/2.5', url = $('meta[data-base]').data('base').replace(/\/$/, ''), storage = sessionStorage || {};

    $('[data-weather]').each(function() {

        var widget = $(this), config = widget.data('weather');

        loadData(config).done(function(data) {

            var unit = config.units == 'metric' ? ' &deg;C' : ' &deg;F',
                location = config.location.split(',');

            $('.js-weather-city', widget).html(location[0]);
            $('.js-weather-country', widget).html(location[1]);
            $('.js-weather-temperatur', widget).html(Math.round(data.main.temp) + unit);
            $('.js-weather-icon', widget).attr('src', getIconUrl(data.weather[0].icon));

        });

    });

    function loadData(config) {

        var key = 'weather-' + config.id, cache = storage[key];

        if (cache) {
            return $.Deferred().resolve(JSON.parse(cache)).promise();
        }

        return $.getJSON(api + '/weather?callback=?', {id: config.id, units: config.units}, function(data) {
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

        return url + '/extensions/system/assets/images/icon-weather-' + icons[icon];
    }

    // widget settings

    var input = $('#form-weather-location'), dropdown, list;

    input.on('keyup', uikit.Utils.debounce(function() {

        $.getJSON(api + '/find?callback=?', {q: input.val(), type: 'like'}, function(data) {
            if (data.cod == 200) {
                showDropdown(data);
            }
        });

    }, 250));

    function showDropdown(data) {

        if (!dropdown) {

            dropdown = $('<div class="uk-dropdown uk-dropdown-search"><ul class="uk-nav uk-nav-search"></ul></div>').appendTo('body').hide();

            list = $('.uk-nav-search', dropdown).on('click', 'li', function() {
                input.val($('a', this).text()).next().val($(this).data('id'));
                dropdown.hide();
            });

            $('body').on('click', function() {
                dropdown.hide();
            });

        }

        list.empty();

        $.each(data.list, function(key, value) {
            list.append('<li data-id="' + value.id + '"><a>' + value.name + ', ' + value.sys.country + '</a></li>');
        });

        dropdown.css({'top':input.offset().top + input.outerHeight(), 'left': input.offset().left}).show();
    }

});
