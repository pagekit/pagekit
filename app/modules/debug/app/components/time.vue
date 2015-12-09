<template>

    <a title="Time"><span class="pf-icon pf-icon-time"></span>{{ duration_str }}</a>

</template>

<script>

    module.exports = {

        section: {
            priority: 30
        },

        props: ['data'],

        data: function () {
            return this.data;
        }

    };

</script>
