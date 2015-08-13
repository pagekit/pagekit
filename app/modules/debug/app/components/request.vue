<template>

    <a title="Request"><div class="pf-icon pf-icon-request"></div> <span class="pf-badge">200</span> @test</a>

</template>

<script>

  module.exports = {

    section: {
        priority: 10
    },

    props: ['data'],

    created: function () {
        this.$data = this.data;
        this.$parent.add(this);
    }

  };

</script>
