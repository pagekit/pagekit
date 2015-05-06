<template>

    {{> settings-fields}}

</template>

<script>

    var Site = require('site');

    module.exports = {

        name: 'page-settings',
        label: 'Settings',
        priority: 0,
        active: 'page',

        template: __vue_template__

    };

    Site.register(module.exports);

</script>
