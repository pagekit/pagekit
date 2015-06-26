<template>

    <div class="uk-form-row" v-partial="settings"></div>

    <div class="uk-form-row">
        <div class="uk-form-controls">

            <v-editor value="{{@ widget.data.content }}" options="{{ {markdown : widget.data.markdown} }}"></v-editor>

            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" name="widget[data][markdown]" v-model="widget.data.markdown"> {{ 'Enable Markdown' | trans }}</label>
            </p>
        </div>
    </div>

</template>

<script>

    module.exports = {

        section: {
            name: 'widget-text',
            label: 'Settings',
            active: 'system/widget-text',
            priority: 0
        },

        props: ['widget', 'config', 'form']

    }

</script>
