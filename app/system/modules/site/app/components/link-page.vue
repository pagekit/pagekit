<template>

    <div class="uk-form-row">
        <label for="form-link-page" class="uk-form-label">{{ 'View' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-link-page" class="uk-width-1-1" v-model="page">
                <option v-for="p in pages" :value="p.id">{{ p.title }}</option>
            </select>
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
            this.$http.get('api/site/page').then(function (res) {
                this.pages = res.data;
                if (this.pages.length) {
                    this.page = this.pages[0].id;
                }
            });
        },

        watch: {

            page: function (page) {
                this.link = '@page/' + page;
            }

        }

    };

    window.Links.components['link-page'] = module.exports;

</script>
