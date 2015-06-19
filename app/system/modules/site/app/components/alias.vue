<template>

    <div class="uk-form-row uk-form-horizontal" v-partial="settings"></div>

    <div class="uk-form-row uk-form-horizontal">
        <label for="form-alias-url" class="uk-form-label">{{ 'Url' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-alias-url" class="uk-form-width-large" type="text" v-model="node.data.url">
        </div>
    </div>

</template>

<script>

    module.exports = {

        props: ['node', 'form', 'type'],

        section: {
            name: 'alias',
            label: 'Settings',
            priority: 0,
            active: 'alias'
        }

    }

</script>
