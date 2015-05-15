<template>
</template>

<script>

  module.exports = {

    paramAttributes: ['data'],

    ready: function () {
        this.$data = this.data;
        this.$parent.add(this, '<a title="Time"><div class="pf-icon pf-icon-time"></div> {{ duration_str }}</a>', {priority: 30});
    }

  };

</script>
