<template>

    <a title="System Information">
        <div class="pf-icon-large pf-icon-pagekit"></div>
    </a>

    <script id="panel-system" type="text/template">

        <h1>Information</h1>

        <h2>System</h2>
        <table class="pf-table pf-table-dropdown">
            <tbody>
            <tr>
                <td>Pagekit</td>
                <td>{{ version }}</td>
            </tr>
            <tr>
                <td>Server</td>
                <td>{{ server }}</td>
            </tr>
            <tr>
                <td>Useragent</td>
                <td>{{ useragent }}</td>
            </tr>
            </tbody>
        </table>

        <h2>PHP</h2>
        <table class="pf-table pf-table-dropdown">
            <tbody>
            <tr>
                <td>PHP</td>
                <td>{{ phpversion }}</td>
            </tr>
            <tr>
                <td>PHP SAPI</td>
                <td>{{ sapi_name }}</td>
            </tr>
            <tr>
                <td>System</td>
                <td>{{ php }}</td>
            </tr>
            <tr>
                <td>Extensions</td>
                <td>{{ extensions }}</td>
            </tr>
            </tbody>
        </table>

        <h2>Database</h2>
        <table class="pf-table pf-table-dropdown">
            <tbody>
            <tr>
                <td>Driver</td>
                <td>{{ dbdriver }}</td>
            </tr>
            <tr>
                <td>Version</td>
                <td>{{ dbversion }}</td>
            </tr>
            <tr>
                <td>Client</td>
                <td>{{ dbclient }}</td>
            </tr>
            </tbody>
        </table>

    </script>

</template>

<script>

    module.exports = {

        section: {
            priority: 10,
            panel: '#panel-system'
        },

        replace: false,

        props: ['data']

    };

</script>
