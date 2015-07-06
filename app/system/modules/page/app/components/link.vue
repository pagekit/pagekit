<template>

    <div class="uk-form-row">
        <label for="form-link-page" class="uk-form-label">{{ 'Page' | trans }}</label>

        <div class="uk-form-controls">
            <select id="form-link-page" class="uk-form-width-large" v-model="page" options="pageOptions"></select>
        </div>
    </div>

</template>

<script>

    module.exports = {

        link: {
            name: 'page',
            label: 'Page'
        },

        props: ['url'],

        data: function () {
            return {
                pages: [],
                page: ''
            }
        },

        created: function () {
            this.$resource('api/page').get(function (pages) {
                this.pages = pages;
            });
        },

        watch: {

            url: {
                handler: function (url) {
                    var matches = (url || '').match(/^@page\/id\?id=(\d+).*/);
                    this.page = matches ? matches[1] : '';
                },
                immediate: true
            },

            page: function (page) {
                this.url = '@page/id?id=' + page;
            }

        },

        computed: {

            pageOptions: function () {
                return [{text: this.$trans('- Select Page -'), value: ''}].concat(_.map(this.pages, function (page) {
                    return {text: page.title, value: page.id};
                }));
            }

        },

        template: __vue_template__

    };

    window.Linkpicker.component('page', module.exports);

</script>
