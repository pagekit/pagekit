<template>

    {{> settings-fields}}

</template>

<script>

    var Site = require('site');

    module.exports = {

        name: 'blog',
        label: 'Settings',
        priority: 0,
        active: 'blog$',

        template: __vue_template__

    };

    Site.register(module.exports);

</script>
