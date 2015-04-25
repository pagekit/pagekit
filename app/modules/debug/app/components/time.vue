<template>
</template>

<script>

  module.exports = {

    ready: function () {
        this.$parent.add(this, '<a title="Time"><div class="pf-icon pf-icon-time"></div> {{ duration_str }}</a>');
    }

  };

</script>