<template>

    <div class="uk-form-row uk-form-horizontal" v-partial="settings"></div>

</template>

<script>

    module.exports = {

        props: ['node', 'form', 'type'],

        section: {
            name: 'blog',
            label: 'Settings',
            priority: 0,
            active: 'blog$'
        }

    };

</script>
