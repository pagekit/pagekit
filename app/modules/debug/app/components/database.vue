<template>

    <a title="Database" class="pf-parent">
        <div class="pf-icon pf-icon-database"></div> {{ nb_statements }}
    </a>

    <div class="pf-dropdown">

        <table class="pf-table pf-table-dropdown">
            <tbody>
                <tr>
                    <td>Queries</td>
                    <td>{{ nb_statements }}</td>
                </tr>
                <tr>
                    <td>Time</td>
                    <td>{{ accumulated_duration_str }}</td>
                </tr>
                <tr>
                    <td>Driver</td>
                    <td>{{ driver }}</td>
                </tr>
            </tbody>
        </table>

    </div>

    <script id="panel-database" type="text/template">

        <h1>Queries</h1>

        <p v-show="!nb_statements">
            <em>No queries.</em>
        </p>

        <div v-repeat="statements">

            <pre><code>{{ sql }}</code></pre>

            <p class="pf-submenu">
                <span>{{ duration_str }}</span>
                <span>{{ params | json }}</span>
            </p>

        </div>

    </script>

</template>

<script>

  module.exports = {

    section: {
        priority: 50,
        panel: '#panel-database'
    },

    props: ['data'],

    created: function () {
        this.$data = this.data;
        this.$parent.add(this);
    }

  };

</script>
