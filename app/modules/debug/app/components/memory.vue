<template>
</template>

<script>

  module.exports = {

    paramAttributes: ['data'],

    ready: function () {
        this.$data = this.data;
        this.$parent.add(this, '<a title="Memory"><div class="pf-icon pf-icon-memory"></div> {{ peak_usage_str }}</a>', {priority: 40});
    }

  };

</script>