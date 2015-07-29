<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Title' | trans }}</span>
            <div class="uk-form-controls uk-form-controls-text">
                <label><input type="checkbox" value="center-content" v-model="widget.data.title_hide"> {{ 'Hide Title' | trans }}</label>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-theme-badge" class="uk-form-label">{{ 'HTML Class' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-theme-badge" class="uk-form-width-large" type="text" v-model="widget.data.html_class">
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-theme-panel" class="uk-form-label">{{ 'Panel Style' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-theme-panel" class="uk-form-width-large" v-model="widget.theme.panel">
                    <option value="">{{ 'None' | trans }}</option>
                    <option value="uk-panel-box">{{ 'Box' | trans }}</option>
                    <option value="uk-panel-box uk-panel-box-primary">{{ 'Box Primary' | trans }}</option>
                    <option value="uk-panel-box uk-panel-box-secondary">{{ 'Box Secondary' | trans }}</option>
                    <option value="uk-panel-header">{{ 'Header' | trans }}</option>
                </select>
            </div>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Alignment' | trans }}</span>
            <div class="uk-form-controls uk-form-controls-text">
                <label><input type="checkbox" value="center-content" v-model="widget.theme.alignment"> {{ 'Center the title and content.' | trans }}</label>
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

        props: ['widget', 'config']

    };

    window.Widgets.components['appearance'] = module.exports;

</script>
