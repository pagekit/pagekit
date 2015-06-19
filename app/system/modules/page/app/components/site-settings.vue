<template>

    <div class="uk-form-row uk-form-horizontal" v-partial="settings"></div>

</template>

<script>

    module.exports = {

        props: ['node', 'form', 'type'],

        section: {
            name: 'page-settings',
            label: 'Settings',
            priority: 0,
            active: 'page'
        }

    };

</script>
