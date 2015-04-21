<template>

    <h1>Routes</h1>

    <table class="pf-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Pattern</th>
                <th>Controller</th>
            </tr>
        </thead>
        <tbody>
            <tr v-repeat="routes">
                <td>{{ name }}</td>
                <td>{{ pattern }} {{ methods | str }}</td>
                <td><abbr title="{{ controller }}">{{ controller | short }}</abbr></td>
            </tr>
        </tbody>
    </table>

</template>

<script>

  module.exports = {

    ready: function () {
      this.$parent.add(this, '<a title="Routes"><div class="pf-icon pf-icon-routes"></div> Routes</a>', 'routes');
    },

    filters: {

        str: function (methods) {
            return methods.length ? '(' + methods + ')' : '';
        },

        short: function (controller) {
            return controller.split('\\').pop();
        }

    }

  };

</script>