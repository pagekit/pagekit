<template>

    <a title="Request"><span class="pf-icon pf-icon-request"></span> <span class="pf-badge">200</span> @test</a>

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
