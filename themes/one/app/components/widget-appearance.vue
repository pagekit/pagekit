<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Title' | trans }}</span>
            <div class="uk-form-controls uk-form-controls-text">
                <label><input type="checkbox" value="center-content" v-model="widget.theme.title_hide"> {{ 'Hide Title' | trans }}</label>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-theme-title-size" class="uk-form-label">{{ 'Title Size' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-theme-title-size" class="uk-form-width-large" v-model="widget.theme.title_size">
                    <option value="uk-panel-title">{{ 'Default' | trans }}</option>
                    <option value="uk-h1 uk-margin-top-remove">{{ 'H1' | trans }}</option>
                    <option value="uk-h2 uk-margin-top-remove">{{ 'H2' | trans }}</option>
                    <option value="uk-h3 uk-margin-top-remove">{{ 'H3' | trans }}</option>
                    <option value="uk-h4 uk-margin-top-remove">{{ 'H4' | trans }}</option>
                    <option value="uk-heading-large uk-margin-top-remove">Extra Large</option>
                </select>
            </div>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Alignment' | trans }}</span>
            <div class="uk-form-controls uk-form-controls-text">
                <label><input type="checkbox" value="center-content" v-model="widget.theme.alignment"> {{ 'Center the title and content.' | trans }}</label>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-theme-badge" class="uk-form-label">{{ 'HTML Class' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-theme-badge" class="uk-form-width-large" type="text" v-model="widget.theme.html_class">
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-theme-panel" class="uk-form-label">{{ 'Panel Style' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-theme-panel" class="uk-form-width-large" v-model="widget.theme.panel" options="panelOptions">
                    <option value="">{{ 'None' | json }}</option>
                </select>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Appearance',
            priority: 90
        },

        props: ['widget', 'config'],


        created: function () {
            this.$set('panelOptions', [
                {value: 'uk-panel-box', text: this.$trans('Box')},
                {value: 'uk-panel-box uk-panel-box-primary', text: this.$trans('Box Primary')},
                {value: 'uk-panel-box uk-panel-box-secondary', text: this.$trans('Box Secondary')},
                {value: 'uk-panel-header', text: this.$trans('Header')}
            ]);
        }

    };

    window.Widgets.components['appearance'] = module.exports;

</script>
