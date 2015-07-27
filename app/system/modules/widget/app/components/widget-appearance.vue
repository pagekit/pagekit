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

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Appearance',
            priority: 90
        },

        props: ['widget']

    }

</script>
