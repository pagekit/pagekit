<template>

    <div class="uk-form-row">
        <label for="form-link-page" class="uk-form-label">{{ 'View' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-link-page" class="uk-width-1-1" v-model="page" options="pageOptions"></select>
        </div>
    </div>

</template>

<script>

    module.exports = {

        link: {
            label: 'Page'
        },

        props: ['link'],

        data: function () {
            return {
                pages: [],
                page: ''
            }
        },

        created: function () {
            //TODO don't retrieve entire page objects
            this.$resource('api/site/page').get(function (pages) {
                this.pages = pages;
                if (pages.length) {
                    this.page = pages[0].id;
                }
            });
        },

        watch: {

            page: function (page) {
                this.link = '@page/' + page;
            }

        },

        computed: {

            pageOptions: function () {
                return _.map(this.pages, function (page) {
                    return {text: page.title, value: page.id};
                });
            }

        }

    };

    window.Links.components['link-page'] = module.exports;

</script>
