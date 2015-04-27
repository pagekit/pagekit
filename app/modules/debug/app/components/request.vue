<template>
</template>

<script>

  module.exports = {

    ready: function () {
      this.$parent.add(this, '<a title="Request"><div class="pf-icon pf-icon-request"></div> <span class="pf-badge">200</span> @test</a>', {priority: 10});
    }

  };

</script>