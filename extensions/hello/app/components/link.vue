<template>

    <div class="uk-form-row">
        <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-name" type="text" class="uk-form-width-large" v-model="name"></input>
        </div>
    </div>

</template>

<script>

    module.exports = {

        link: {
            name: 'hello',
            label: 'Hello'
        },

        props: ['url'],

        data: function () {
            return {
                name: []
            }
        },

        watch: {

            name: function (name) {
                this.url = '@hello/name?name=' + name;
            }

        },

        template: __vue_template__

    };

    window.Links.component('hello', module.exports);

</script>
