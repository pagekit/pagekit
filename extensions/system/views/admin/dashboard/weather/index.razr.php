@script('weather', 'system/js/dashboard/weather.js', ['requirejs'])

<div class="uk-text-center" data-weather="@(['id' => widget.settings.id, 'units' => widget.settings.units, 'location' => widget.settings.location]|json_encode|e)">
    <div class="pk-weather-image">
    	<img class="js-weather-icon uk-text-top">
    	<span class="js-weather-temperatur uk-text-large uk-text-muted pk-weather-temperatur"></span>
    </div>
    <h1 class="js-weather-city uk-h2 uk-margin-remove"></h1>
    <h2 class="js-weather-country uk-h3 uk-margin-remove uk-text-muted"></h2>
    <div class="js-error uk-hidden uk-alert uk-alert-danger">@trans('Unable to retrieve weather data.')</div>
</div>