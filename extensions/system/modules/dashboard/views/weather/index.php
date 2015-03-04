<?php $app['scripts']->add('weather', 'extensions/system/modules/dashboard/app/weather.js', 'vue-system') ?>

<div class="uk-text-center" data-weather="<?= $this->escape(json_encode(['id' => $widget->get('id'), 'units' => $widget->get('units'), 'location' => $widget->get('location')])) ?>" v-cloak>
    <div class="uk-text-center" v-show="status == 'loading'">
        <i class="uk-icon-medium uk-icon-spinner uk-icon-spin"></i>
    </div>
    <div class="uk-alert uk-alert-danger" v-show="status == 'error'">{{ 'Unable to retrieve weather data.' | trans }}</div>
    <div class="pk-weather-image">
    	<img class="uk-text-top" v-attr="src: icon">
    	<span class="uk-text-large uk-text-muted pk-weather-temperature">{{ temperature }}</span>
    </div>
    <h1 class="uk-h2 uk-margin-remove">{{ city }}</h1>
    <h2 class="uk-h3 uk-margin-remove uk-text-muted">{{ country }}</h2>
</div>
