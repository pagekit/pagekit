<template>

    <a title="Memory"><span class="pf-icon pf-icon-memory"></span> {{ data.peak_usage_str }}</a>

</template>

<script>

    module.exports = {

        section: {
            priority: 40
        },

        replace: false,

        props: ['data']

    };

</script>
