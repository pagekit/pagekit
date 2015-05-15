<template>
</template>

<script>

  module.exports = {

    paramAttributes: ['data'],

    ready: function () {
        this.$data = this.data;
        this.$parent.add(this, '<a title="Events"><div class="pf-icon pf-icon-events"></div> Events</a>', {priority: 10});
    }

  };

</script>