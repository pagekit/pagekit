<template>

    <a :class="link.class" :href="link.link" @click.prevent="$parent.openModal(link)"><span v-html="link.txt" v-if="link.txt"></span><span v-else>{{ 'Select Link' | trans }}</span> <span class="pk-icon-link pk-icon-hover"></span></a>

</template>

<script>

    module.exports = {

        props: ['index'],

        computed: {

            link: function() {
                return this.$parent.links[this.index] || {};
            }

        }

    };

</script>
