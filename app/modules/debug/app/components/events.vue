<template>

    <a title="Events"><div class="pf-icon pf-icon-events"></div> Events</a>

</template>

<script>

  module.exports = {

    section: {
        priority: 10
    },

    replace: true,

    props: ['data'],

    ready: function () {
        this.$data = this.data;
        this.$parent.add(this);
    }

  };

</script>
