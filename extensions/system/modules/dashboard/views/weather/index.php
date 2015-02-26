<?php $app['scripts']->queue('weather', 'extensions/system/modules/dashboard/assets/js/weather.js', ['requirejs']) ?>

<div class="uk-text-center" data-weather="<?= $this->escape(json_encode(['id' => $widget->get('id'), 'units' => $widget->get('units'), 'location' => $widget->get('location')])) ?>">
    <div class="js-spinner uk-text-center"><i class="uk-icon-medium uk-icon-spinner uk-icon-spin"></i></div>
    <div class="pk-weather-image">
    	<img class="js-weather-icon uk-text-top">
    	<span class="js-weather-temperature uk-text-large uk-text-muted pk-weather-temperature"></span>
    </div>
    <h1 class="js-weather-city uk-h2 uk-margin-remove"></h1>
    <h2 class="js-weather-country uk-h3 uk-margin-remove uk-text-muted"></h2>

    <h1 class="js-error uk-hidden uk-h3"><?= __('Weather') ?></h1>
    <div class="js-error uk-hidden uk-alert uk-alert-danger"><?= __('Unable to retrieve weather data.') ?></div>
</div>