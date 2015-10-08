<template>

    <a class="uk-placeholder uk-text-center uk-display-block uk-margin-remove" v-if="!image.src" v-on="click: pick()">
        <img width="60" height="60" alt="{{ 'Placeholder Image' | trans }}" v-attr="src: $url('app/system/assets/images/placeholder-image.svg')">
        <p class="uk-text-muted uk-margin-small-top">{{ 'Add Image' | trans }}</p>
    </a>

    <div class="uk-overlay uk-overlay-hover uk-visible-hover {{ class }}" v-if="image.src">

        <img v-attr="src: $url(image.src)">

        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade"></div>

        <a class="uk-position-cover" v-on="click: pick()"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()"></a></li>
            </ul>
        </div>

    </div>

    <v-modal v-ref="modal">
        <form class="uk-form uk-form-stacked" v-on="submit: update">

            <div class="uk-modal-header">
                <h2>{{ 'Image' | trans }}</h2>
            </div>

            <div class="uk-form-row">
                <input-image source="{{@ img.src }}"></input-image>
            </div>

            <div class="uk-form-row">
                <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-src" class="uk-width-1-1" type="text" v-model="img.src" lazy>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-alt" class="uk-form-label">{{ 'Alt' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-alt" class="uk-width-1-1" type="text" v-model="img.alt">
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" type="button" v-on="click: update()">{{ 'Update' | trans }}</button>
            </div>

        </form>
    </v-modal>

</template>

<script>

    module.exports = {

        props: ['class', 'image'],

        data: function () {
            return _.merge({
                'class': ''
            }, $pagekit);
        },

        ready: function () {

            this.image = this.image && typeof(this.image) == 'object' ? this.image : {src: '', alt: ''};
            this.$set('img', {src: this.image.src, alt: this.image.alt});

            this.$on('image-selected', function(path) {

                if (path && !this.img.alt) {

                    var alt   = path.split('/').slice(-1)[0].replace(/\.(jpeg|jpg|png|svg|gif)$/i, '').replace(/(_|-)/g, ' ').trim(),
                        first = alt.charAt(0).toUpperCase();

                    this.img.alt = first + alt.substr(1);
                }
            });
        },

        methods: {

            pick: function() {
                this.img.src = this.image.src;
                this.img.alt = this.image.alt;
                this.$.modal.open();
            },

            update: function() {
                this.image.src = this.img.src;
                this.image.alt = this.img.alt;
                this.$.modal.close();
            },

            remove: function() {
                this.img.src = '';
                this.image.src = '';
            }
        }

    };

    Vue.component('input-image-meta', function (resolve, reject) {
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
