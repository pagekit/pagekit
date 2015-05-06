<template>

    {{> settings-fields}}

</template>

<script>

    module.exports = {

        name: 'blog',
        label: 'Settings',
        priority: 0,
        active: 'blog$',

        template: __vue_template__

    };

</script>
