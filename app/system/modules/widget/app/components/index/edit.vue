<template>

    <div class="uk-modal" v-el="modal">

        <div class="uk-modal-dialog uk-modal-dialog-large">

            <form class="uk-form uk-container uk-container-center" name="widgetform" v-if="widget" v-on="valid: save">

                <div class="uk-clearfix uk-margin" data-uk-margin>

                    <div class="uk-float-left">

                        <h2 class="uk-h2" v-if="widget.id">{{ widget.title }} ({{ typeName }})</h2>
                        <h2 class="uk-h2" v-if="!widget.id">{{ 'Add %type%' | trans {type:typeName} }}</h2>

                    </div>

                    <div class="uk-float-right">

                        <a class="uk-button" v-on="click: cancel()">{{ 'Cancel' | trans }}</a>
                        <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

                    </div>

                </div>

                <widget-edit widget="{{ widget }}" config="{{ widgetConfig }}" position="{{ position }}" form="{{ widgetform }}" v-ref="edit"></widget-edit>

            </form>

        </div>
    </div>

</template>

<script>

    var _ = require('lodash');
    var UIkit = require('uikit');

    module.exports = {

        inherit: true,

        created: function () {
            var container = document.createElement('div');
            document.body.appendChild(container);
            this.$mount(container);
        },

        ready: function() {

            var self = this;

            this.modal = UIkit.modal(this.$$.modal);
            this.modal.on('hide.uk.modal', function() {
                self.$set('widget', null);
            });
        },

        watch: {

            widget: function (widget) {
                if (widget) {
                    this.modal.show();
                }
            }

        },

        events: {

            saved: function() {

                this.load();
                this.cancel();

            }

        },

        computed: {

            type: function() {
                return _.find(this.config.types, { id: this.widget.type });
            },

            typeName: function() {
                return this.type ? this.type.name : this.$trans('Extension not loaded');
            },

            position: function() {

                var id = this.widget.id;

                var position = _.find(this.positions, function(position) {
                    return _.find(position.widgets, { id: id });
                });

                return position && position.id;
            },

            widgetConfig: function() {
                return _.defaults({}, this.config.configs[this.widget.id], this.config.configs.defaults);
            }

        },

        methods: {

            save: function (e) {
                this.$.edit.save(e);
            },

            cancel: function() {
                this.modal.hide();
            }

        },

        components: {

            'widget-edit': require('../edit/edit.vue')

        }
    };

</script>
