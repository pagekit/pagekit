<template>

    <a title="Memory"><div class="pf-icon pf-icon-memory"></div> {{ peak_usage_str }}</a>

</template>

<script>

  module.exports = {

    section: {
        priority: 40
    },

    props: ['data'],

    created: function () {
        this.$data = this.data;
        this.$parent.add(this);
    }

  };

</script>
