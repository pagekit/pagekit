<template>

    <div v-attr="class: class">
        <div class="pk-form-link uk-width-1-1">
            <input class="uk-width-1-1" type="text" v-model="link" v-attr="name: name, id: id" v-validate="required: isRequired" v-el="input" lazy>
            <a class="pk-form-link-toggle pk-link-icon uk-flex-middle" v-on="click: open">{{ 'Select' | trans }} <i class="pk-icon-link pk-icon-hover uk-margin-small-left"></i></a>
        </div>
    </div>

    <p class="uk-text-muted uk-margin-small-top uk-margin-bottom-remove" v-show="url">{{ url }}</p>

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
                    this.$http.get('api/site/link', {link: this.link}, function (data) {
                        this.url = data.url ? data.url : false;
                    }).error(function () {
                        this.url = false;
                    })
                } else {
                    this.url = false;
                }
            },

            open: function (e) {
                e.preventDefault();
                this.$.modal.open();
            },

            update: function (e) {
                e.preventDefault();

                this.$set('link', this.$.links.link);

                Vue.nextTick(function() {
                    this.$$.input.dispatchEvent(new Event('input'));
                }.bind(this));

                this.$.modal.close();
            },

            showUpdate: function () {
                return !!this.$.links.link;
            }

        }

    };

    Vue.component('input-link', function (resolve) {
        resolve(module.exports);
    });

</script>
