<template>

    <a :title="'Requests' | trans">Requests</a>

    <div class="pf-dropdown">

        <table class="pf-table pf-table-dropdown">
            <tbody>
            <tr>
                <td>Request Id</td>
                <td>{{ $root.data.__meta.id }}</td>
            </tr>
            </tbody>
        </table>

    </div>

    <script id="panel-requests" type="text/template">

        <h1>Requests</h1>
        <table class="pf-table">
            <thead>
                <tr>
                    <th>IP</th>
                    <th>Method</th>
                    <th>URL</th>
                    <th>Time</th>
                    <th>Request Id</th>
                </tr>
            </thead>
            <tbody>
                <tr :class="{'pf-active': $root.request.id === $root.data.__meta.id}">
                    <td>{{ $root.request.ip }}</td>
                    <td>{{ $root.request.method }}</td>
                    <td>{{ $root.request.uri }}</td>
                    <td>{{ $root.request.datetime }}</td>
                    <td><a @click.prevent="$root.load($root.request.id)">{{ $root.request.id }}</a></td>
                </tr>
                <tr v-for="request in requests" :class="{'pf-active': request.id === $root.data.__meta.id}">
                    <td>{{ request.ip }}</td>
                    <td>{{ request.method }}</td>
                    <td>{{ request.uri }}</td>
                    <td>{{ request.datetime }}</td>
                    <td><a @click.prevent="$root.load(request.id)">{{ request.id }}</a></td>
                </tr>
            </tbody>
        </table>

    </script>

</template>

<script>

    module.exports = {

        section: {
            priority: 80,
            panel: '#panel-requests'
        },

        replace: false,

        props: ['data']

    };

</script>
