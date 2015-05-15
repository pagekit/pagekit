<template>

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

</template>

<script>

  module.exports = {

    paramAttributes: ['data'],

    ready: function () {
        this.$data = this.data;
        this.$parent.add(this, '<a title="Log">Log ({{ records.length }})</a>', {priority: 70, panel: 'log'});
    }

  };

</script>
