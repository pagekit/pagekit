<template>

    <div class="uk-panel uk-placeholder uk-placeholder-large uk-text-center uk-visible-hover" v-if="!image.data.src">

        <img width="60" height="60" :alt="'Placeholder Image' | trans" :src="$url('app/system/assets/images/placeholder-image.svg')">
        <p class="uk-text-muted uk-margin-small-top">{{ 'Add Image' | trans }}</p>

        <a class="uk-position-cover" @click.prevent="config"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="remove"></a></li>
            </ul>
        </div>

    </div>

    <div class="uk-overlay uk-overlay-hover uk-visible-hover" v-else>

        <img :src="$url(image.data.src)" :alt="image.data.alt">

        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade"></div>

        <a class="uk-position-cover" @click.prevent="config"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="remove"></a></li>
            </ul>
        </div>

    </div>

</template>

<script>

    module.exports = {

        props: ['index'],

        computed: {

            image: function() {
                return this.$parent.images[this.index] || {};
            }

        },

        methods: {

            config: function() {
                this.$parent.openModal(this.image);
            },

            remove: function() {
                this.image.replace('');
            }

        }

    };

</script>
