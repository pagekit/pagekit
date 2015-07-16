<template>

    <div class="uk-form-horizontal">

        <partial name="settings"></partial>

    </div>

</template>

<script>

    module.exports = {

        props: ['node', 'form', 'type'],

        section: {
            label: 'Settings',
            priority: 0,
            active: 'hello$'
        },

        template: __vue_template__

    };

    window.Site.component('hello', module.exports);

</script>
