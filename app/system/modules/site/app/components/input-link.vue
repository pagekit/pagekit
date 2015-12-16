<template>

    <div :class="[class]">
        <div class="pk-form-link uk-width-1-1">
            <input class="uk-width-1-1" type="text" v-model="link" :id="id" :name="name" v-validate:required="isRequired" v-el:input lazy>
            <a class="pk-form-link-toggle pk-link-icon uk-flex-middle" @click.prevent="open">{{ 'Select' | trans }} <i class="pk-icon-link pk-icon-hover uk-margin-small-left"></i></a>
        </div>
    </div>

    <p class="uk-text-muted uk-margin-small-top uk-margin-bottom-remove" v-show="url">{{ url }}</p>

    <v-modal v-ref:modal>

        <form class="uk-form uk-form-stacked" @submit.prevent="update">

            <div class="uk-modal-header">
                <h2>{{ 'Select Link' | trans }}</h2>
            </div>

            <panel-link v-ref:links></panel-link>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" type="submit" :disabled="!showUpdate()">{{ 'Update' | trans }}</button>
            </div>

        </form>

    </v-modal>

</template>

<script>

    module.exports = {

        props: ['link', 'name', 'class', 'id', 'required'],

        data: function () {
            return {url: false};
        },

        watch: {

            link: {
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
                if (this.link) {
                    this.$http.get('api/site/link', {link: this.link}).then(function (res) {
                                this.url = res.data.url || false;
                            }, function () {
                                this.url = false;
                            });
                } else {
                    this.url = false;
                }
            },

            open: function () {
                this.$refs.modal.open();
            },

            update: function () {
                this.$set('link', this.$refs.links.link);

                Vue.nextTick(function() {
                    this.$els.input.dispatchEvent(new Event('input'));
                }.bind(this));

                this.$refs.modal.close();
            },

            showUpdate: function () {
                return !!this.$refs.links.link;
            }

        }

    };

    Vue.component('input-link', function (resolve) {
        resolve(module.exports);
    });

</script>
