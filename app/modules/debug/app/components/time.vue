<template>

    <a title="Time"><span class="pf-icon pf-icon-time"></span>{{ data.duration_str }}</a>

</template>

<script>

    module.exports = {

        section: {
            priority: 30
        },

        props: ['data']

    };

</script>
