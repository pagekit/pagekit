<template>
</template>

<script>

  module.exports = {

    ready: function () {
      this.$parent.add(this, '<a title="Memory"><div class="pf-icon pf-icon-memory"></div> {{ peak_usage_str }}</a>');
    }

  };

</script>