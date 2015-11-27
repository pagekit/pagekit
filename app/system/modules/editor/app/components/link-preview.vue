<template>

    <a :class="link.class" :href="link.link" @click.prevent="$parent.openModal(link)">{{{ link.txt ? link.txt : 'Select Link' | trans }}} <span class="pk-icon-link pk-icon-hover"></span></a>

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
