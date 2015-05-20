<template>

    {{> settings}}

</template>

<script>

    module.exports = {

        paramAttributes: ['node', 'form', 'type'],

        section: {
            name: 'blog',
            label: 'Settings',
            priority: 0,
            active: 'blog$'
        }

    };

</script>
