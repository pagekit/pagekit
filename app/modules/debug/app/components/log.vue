<template>

    <a title="Log">Log ({{ records.length }})</a>

    <script id="panel-log" type="text/template">

        <h1>Logs</h1>

        <table class="pf-table">
            <thead>
                <tr>
                    <th>Message</th>
                    <th>Level</th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="records">
                    <td>{{ message }}</td>
                    <td>{{ level_name }}</td>
                </tr>
            </tbody>
        </table>

    </script>

</template>

<script>

  module.exports = {

    section: {
        priority: 70,
        panel: '#panel-log'
    },

    props: ['data'],

    created: function () {
        this.$data = this.data;
        this.$parent.add(this);
    }

  };

</script>
