<div v-component="v-locale" inline-template>

    <h2 class="pk-form-heading">{{ 'Localization' | trans }}</h2>
    <div class="uk-form-row">

        <label for="form-sitelocale" class="uk-form-label">{{ 'Site Locale' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-sitelocale" class="uk-form-width-large" v-model="option['system/locale'].locale" options="$toOptions(locales)"></select>
        </div>
    </div>
    <div class="uk-form-row">
        <label for="form-adminlocale" class="uk-form-label">{{ 'Admin Locale' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-adminlocale" class="uk-form-width-large" v-model="option['system/locale'].locale_admin" options="$toOptions(locales)"></select>
        </div>
    </div>
    <div class="uk-form-row">
        <label for="form-timezone" class="uk-form-label">{{ 'Time Zone' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-timezone" class="uk-form-width-large" v-model="option['system/locale'].timezone" options="$toOptions(timezones)"></select>
        </div>
    </div>

</div>
