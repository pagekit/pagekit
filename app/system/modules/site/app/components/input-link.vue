<template>

    <div v-attr="class: class">
        <div class="pk-form-link uk-width-1-1">
            <input class="uk-width-1-1" type="text" v-model="url" v-attr="name: name, id: id" v-if="!isRequired">
            <input class="uk-width-1-1" type="text" v-model="url" v-attr="name: name, id: id" v-if="isRequired" v-valid="required">
            <a class="pk-form-link-toggle pk-link-icon uk-flex-middle" v-on="click: open">{{ 'Select' | trans }} <i class="pk-icon-edit pk-icon-hover uk-margin-small-left"></i></a>
        </div>
    </div>

    <p class="uk-text-muted uk-margin-small-top" v-show="link">{{ link }}</p>

    <v-modal v-ref="modal">

        <form class="uk-form uk-form-stacked" v-on="submit: update">

            <div class="uk-modal-header">
                <h2>{{ 'Select Link' | trans }}</h2>
            </div>

            <panel-link v-ref="links"></panel-link>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" type="submit" v-attr="disabled: !showUpdate()">{{ 'Update' | trans }}</button>
            </div>

        </form>

    </v-modal>

</template>

<script>

    module.exports = Vue.extend({

        props: ['url', 'name', 'class', 'id', 'required'],

        data: function () {
            return {link: false};
        },

        watch: {

            url: {
                handler: 'load',
                immediate: true
            }

        },

        computed: {

            isRequired: function() {
                return this.required !== undefined;
            }

        },

        methods: {

            load: function () {
                if (this.url) {
                    this.$http.get('api/site/link', {link: this.url}, function (data) {
                        this.link = data.url ? data.url : false;
                    }).error(function () {
                        this.link = false;
                    })
                } else {
                    this.link = false;
                }

            },

            open: function (e) {
                e.preventDefault();
                this.$.modal.open();
            },

            update: function (e) {
                e.preventDefault();

                this.$set('url', this.$.links.url);
                this.$.modal.close();
            },

            showUpdate: function () {
                return !!this.$.links.url;
            }

        }

    });

    Vue.component('input-link', module.exports);

</script>
