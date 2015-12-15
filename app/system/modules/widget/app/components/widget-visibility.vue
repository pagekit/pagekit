<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row">
            <span class="uk-form-label">Pages</span>
            <div class="uk-form-controls uk-form-controls-text" v-if="config.menus">

                <input-tree :active.sync="widget.nodes"></input-tree>

            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Visibility',
            priority: 100
        },

        data: function () {
          return {
              menus: false
          }
        },

        props: ['widget', 'config', 'form']

    }

</script>
