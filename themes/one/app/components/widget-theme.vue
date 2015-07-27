<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row">
            <label for="form-theme-panel" class="uk-form-label">{{ 'Panel Style' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-theme-panel" class="uk-form-width-large" v-model="widget.theme.panel">
                    <option value="">{{ 'None' | trans }}</option>
                    <option value="uk-panel-box">{{ 'Box' | trans }}</option>
                    <option value="uk-panel-box uk-panel-box-primary">{{ 'Box Primary' | trans }}</option>
                    <option value="uk-panel-box uk-panel-box-secondary">{{ 'Box Secondary' | trans }}</option>
                    <option value="uk-panel-header">{{ 'Header' | trans }}</option>
                    <option value="uk-panel-space">{{ 'Space' | trans }}</option>
                </select>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-theme-badge" class="uk-form-label">{{ 'Badge' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-theme-badge" class="uk-form-width-small" type="text" v-model="widget.theme.badge.text">
                <select class="uk-form-width-small" v-model="widget.theme.badge.type">
                    <option value="uk-panel-badge uk-badge">{{ 'Default' | trans }}</option>
                    <option value="uk-panel-badge uk-badge uk-badge-success">{{ 'Success' | trans }}</option>
                    <option value="uk-panel-badge uk-badge uk-badge-warning">{{ 'Warning' | trans }}</option>
                    <option value="uk-panel-badge uk-badge uk-badge-danger">{{ 'Danger' | trans }}</option>
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
            name: 'theme-settings',
            label: 'Theme',
            priority: 50
        },

        props: ['widget', 'config']

    };

    window.Widgets.components['theme-settings'] = module.exports;

</script>
