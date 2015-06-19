<template>

    <div class="uk-grid pk-grid-large uk-form-stacked" data-uk-grid-margin>
        <div class="uk-flex-item-1">

            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="page[title]" placeholder="{{ 'Enter Title' | trans }}" v-model="page.title">
            </div>

            <div class="uk-form-row">
                <v-editor name="page[content]" value="{{@ page.content }}" options="{{ {markdown : page.data.markdown} }}"></v-editor>
            </div>

        </div>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Options' | trans }}</span>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" name="page[data][title]" v-model="page.data.title"> {{ 'Show Title' | trans }}</label>
                    </div>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" name="page[data][markdown]" v-model="page.data.markdown"> {{ 'Enable Markdown' | trans }}</label>
                    </div>
                </div>

            </div>

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
