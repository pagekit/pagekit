<template>

    {{> settings}}

    <div class="uk-form-row">
        <div class="uk-form-controls">

            <v-editor value="{{ widget.settings.content }}" options="{{ {markdown : widget.settings.markdown} }}"></v-editor>

            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" name="widget[settings][markdown]" v-model="widget.settings.markdown"> {{ 'Enable Markdown' | trans }}</label>
            </p>
        </div>
    </div>

</template>

<script>

    module.exports = {

        section: {
            name: 'site-text',
            label: 'Settings',
            active: 'site.text',
            priority: 0
        },

        paramAttributes: ['widget', 'config', 'form']

    }

</script>
