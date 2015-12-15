<template>

    <a title="Memory"><span class="pf-icon pf-icon-memory"></span> {{ peak_usage_str }}</a>

</template>

<script>

    module.exports = {

        section: {
            priority: 40
        },

        props: ['data'],

        data: function () {
            return this.data;
        }

    };

</script>
