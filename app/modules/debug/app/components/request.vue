<template>
</template>

<script>

  module.exports = {

    paramAttributes: ['data'],

    ready: function () {
        this.$data = this.data;
        this.$parent.add(this, '<a title="Request"><div class="pf-icon pf-icon-request"></div> <span class="pf-badge">200</span> @test</a>', {priority: 10});
    }

  };

</script>