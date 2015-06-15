<template>

    {{> settings}}

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
