<template>

    <div class="uk-form-horizontal">
        <partial name="settings"></partial>
    </div>

</template>

<script>

    module.exports = {

        props: ['node', 'roles'],

        section: {
            label: 'Settings'
        },

        created: function () {
            this.$options.partials.settings = this.$root.$options.partials.settings;
        }

    };

</script>
