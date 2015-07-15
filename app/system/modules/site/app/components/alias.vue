<template>

    <div class="uk-form-horizontal">

        <partial name="settings"></partial>

        <div class="uk-form-row">
            <label for="form-url" class="uk-form-label">{{ 'Url' | trans }}</label>
            <div class="uk-form-controls">
                <input-link id="form-url" class="uk-form-width-large" name="url" url="{{@ node.data.url}}" required></input-link>
                <div class="uk-form-help-block uk-text-danger" v-show="form.url.invalid">{{ 'Invalid url.' | trans }}</div>
            </div>
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
