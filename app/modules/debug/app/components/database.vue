<template>

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

    <div v-el="navbar" style="display: none">

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

    </div>

</template>

<script>

  module.exports = {

    ready: function () {
      this.$parent.add(this, $(this.$$.navbar).html(), {priority: 50, panel: 'database'});
    }

  };

</script>