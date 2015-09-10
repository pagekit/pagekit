<template>

    <a class="uk-placeholder uk-text-center uk-display-block uk-margin-remove" v-if="!source" v-on="click: pick()">
        <img width="60" height="60" alt="{{ 'Placeholder Image' | trans }}" v-attr="src: $url('app/system/assets/images/placeholder-image.svg')">
        <p class="uk-text-muted uk-margin-small-top">{{ 'Select Image' | trans }}</p>
    </a>

    <div class="uk-overlay uk-overlay-hover uk-visible-hover {{ class }}" v-if="source">

        <img v-attr="src: $url(source)">

        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade"></div>

        <a class="uk-position-cover" v-on="click: pick()"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()"></a></li>
            </ul>
        </div>

    </div>

    <v-modal v-ref="modal" large>

        <panel-finder root="{{ storage }}" v-ref="finder" modal="true"></panel-finder>

        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
            <button class="uk-button uk-button-primary" type="button" v-attr="disabled: !hasSelection()" v-on="click: select()">{{ 'Select' | trans }}</button>
        </div>

    </v-modal>

</template>

<script>

    module.exports = {

        props: ['class', 'source'],

        data: function () {
            return _.merge({
                'class': '',
                'source': ''
            }, $pagekit);
        },

        methods: {

            pick: function() {
                this.$.modal.open();
            },

            select: function() {
                this.source = this.$.finder.getSelected()[0];
                this.$dispatch('image-selected', this.source);
                this.$.finder.removeSelection();
                this.$.modal.close();
            },

            remove: function() {
                this.source = ''
            },

            hasSelection: function() {
                var selected = this.$.finder.getSelected();
                return selected.length === 1 && this.$.finder.isImage(selected[0])
            }

        }

    };

    Vue.component('input-image', function (resolve, reject) {
        Vue.asset({
            js: [
                'app/assets/uikit/js/components/upload.min.js',
                'app/system/modules/finder/app/bundle/panel-finder.js'
            ]
        }, function () {
            resolve(module.exports);
        })
    });

</script>
