<template>

    <a title="Time"><div class="pf-icon pf-icon-time"></div> {{ duration_str }}</a>

</template>

<script>

  module.exports = {

    section: {
        priority: 30
    },

    props: ['data'],

    created: function () {
        this.$data = this.data;
        this.$parent.add(this);
    }

  };

</script>
