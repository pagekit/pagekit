<template>

    <div class="uk-form-horizontal">

        <partial name="settings"></partial>

        <div class="uk-form-row">
            <label for="form-url" class="uk-form-label">{{ 'Url' | trans }}</label>
            <div class="uk-form-controls">
                <input-link id="form-url" class="uk-form-width-large" url="{{@ node.data.url}}"></input-link>
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
