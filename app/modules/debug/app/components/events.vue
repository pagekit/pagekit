<template>

    <a title="Events"><span class="pf-icon pf-icon-events"></span> Events</a>

</template>

<script>

    module.exports = {

        section: {
            priority: 10
        },

        props: ['data'],

        data: function () {
            return this.data;
        }

    };

</script>
