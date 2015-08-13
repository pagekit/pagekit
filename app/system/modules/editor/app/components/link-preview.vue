<template>

    <a class="{{ link.class }}" href="{{ link.link }}" v-on="click: config">{{{ link.txt ? link.txt : 'Select Link' | trans }}} <span class="pk-icon-link pk-icon-hover"></span></a>

</template>

<script>

    module.exports = {

        props: ['index'],

        computed: {

            link: function() {
                return this.$parent.links[this.index] || {};
            }

        },

        methods: {

            config: function(e) {
                e.preventDefault();
                this.$parent.openModal(this.link);
            }

        }

    };

</script>
