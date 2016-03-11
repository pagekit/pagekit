<template>

    <div class="uk-form-row">
        <label for="form-link-file" class="uk-form-label">{{ 'File' | trans }}</label>
        <div class="uk-form-controls">
            <div class="pk-form-link uk-width-1-1">
                <input id="form-link-file" class="uk-width-1-1" type="text" v-model="file" v-validate:required="isRequired" v-el:input lazy>
                <a class="pk-form-link-toggle pk-link-icon uk-flex-middle" @click.prevent="pick">{{ 'Select' | trans }} <i class="pk-icon-link pk-icon-hover uk-margin-small-left"></i></a>
            </div>
        </div>
    </div>

    <v-modal v-ref:modal large>

        <panel-finder :root="storage" v-ref:finder :modal="true"></panel-finder>

        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
            <button class="uk-button uk-button-primary" type="button" :disabled="!hasSelection()" @click.prevent="select">{{ 'Select' | trans }}</button>
        </div>

    </v-modal>

</template>

<script>

    module.exports = {

        link: {
            label: 'Storage'
        },

        props: ['link'],

        data: function () {
            return _.merge({
                file: ''
            }, $pagekit);
        },

        created: function () {
            this.assets = this.$asset({
                js: [
                    'app/assets/uikit/js/components/upload.min.js',
                    'app/system/modules/finder/app/bundle/panel-finder.js'
                ]
            });
        },

        watch: {
            file: function (file) {
                this.$set('link', file);
            }
        },

        methods: {

            pick: function () {
                this.assets.then(function () {
                    this.$refs.modal.open();
                });
            },

            select: function () {
                this.file = this.$refs.finder.getSelected()[0];
                this.$refs.finder.removeSelection();
                this.$refs.modal.close();
            },

            hasSelection: function () {
                var selected = this.$refs.finder.getSelected();
                return selected.length === 1;
            }

        }

    };

    window.Links.components['link-storage'] = module.exports;

</script>
