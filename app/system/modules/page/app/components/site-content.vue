<template>

    <div class="uk-form-row">
        <label for="form-page-title" class="uk-form-label">{{ 'Page Title' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-page-title" class="uk-form-width-large" type="text" name="page[title]" v-model="page.title">
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-url" class="uk-form-label">{{ 'Content' | trans }}</label>
        <div class="uk-form-controls">
            <v-editor id="post-content" name="page[content]" value="{{@ page.content }}" options="{{ {markdown : page.data.markdown} }}"></v-editor>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Options' | trans }}</span>
        <div class="uk-form-controls">
            <label><input type="checkbox" name="page[data][title]" v-model="page.data.title"> {{ 'Show Title' | trans }}</label>
        </div>
        <div class="uk-form-controls">
            <label><input type="checkbox" name="page[data][markdown]" v-model="page.data.markdown"> {{ 'Enable Markdown' | trans }}</label>
        </div>
    </div>

</template>

<script>

    module.exports = {

        props: ['node', 'form'],

        section: {
            name: 'page-content',
            label: 'Content',
            priority: 10,
            active: 'page'
        },

        events: {

            save: function(data) {
                data.page = this.page;
            }

        },

        watch: {

            'node.data.variables.id': function(id) {

                if (!id) {
                    this.$set('page', {});
                }

                this.$resource('api/page/:id').get({id: id}, function (page) {
                    this.$set('page', page);
                });

            }

        }

    };

</script>
