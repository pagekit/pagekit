<template>

    <a href="#" v-on="click: open">{{ link ? link : 'Select Link' | trans }} <i class="uk-icon-pencil"></i></a>

    <div class="uk-modal" v-el="modal">
        <form class="uk-modal-dialog uk-form uk-form-stacked">

            <div class="uk-modal-header">
                <h2>{{ 'Select Link' | trans }}</h2>
            </div>

            <linkpicker url="{{ url }}" v-ref="linkpicker"></linkpicker>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" v-on="click: update">{{ 'Update' | trans }}</button>
            </div>

        </form>
    </div>

</template>

<script>

    var Input = Vue.extend({

        props: ['url'],

        data: function() {
            return {link: false};
        },

        compiled: function () {

            this.$addChild({
                el: this.$$.modal,
                inherit: true
            }).$appendTo('body');

        },

        watch: {

            url: {
                handler:'load',
                immediate: true
            }

        },

        methods: {

            load: function() {
                if (this.url) {
                    this.$http.get('api/site/link', {link: this.url}, function(data) {
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

                this.$.linkpicker.url = this.url;
                this.modal = UIkit.modal(this.$$.modal);
                this.modal.show();
            },

            update: function (e) {
                e.preventDefault();
                this.$set('url', this.$.linkpicker.url);
                this.cancel(e);
            },

            cancel: function (e) {
                e.preventDefault();
                this.modal.hide();
            }

        },

        components: {

            linkpicker: window.Linkpicker

        }

    });

    Input.create = function (element, options) {
        return new Input({el: element, data: options});
    };

    Vue.component('input-link', Input);

    $(function () {
        $('[data-linkpicker]').each(function () {
            Input.create(this, $(this).data('link'));
        });
    });

    module.exports = Input;

</script>
