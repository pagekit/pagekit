<template>

    <div class="uk-form-horizontal">

        <partial name="settings"></partial>

        <div class="uk-form-row">
            <label class="uk-form-label">{{ 'Url' | trans }}</label>
            <div class="uk-form-controls uk-form-controls-text">
                <input-link url="{{@ node.data.url}}"></input-link>
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
