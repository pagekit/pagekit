<template>

    <a :title="'Routes' | trans"><span class="pf-icon pf-icon-routes"></span>Routes</a>

    <script id="panel-routes" type="text/template">

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
                <tr v-for="route in routes">
                    <td>{{ route.name }}</td>
                    <td>{{ route.pattern }} {{ route.methods | str }}</td>
                    <td><abbr :title="route.controller">{{ route.controller | short }}</abbr></td>
                </tr>
            </tbody>
        </table>

    </script>

</template>

<script>

    module.exports = {

        section: {
            priority: 20,
            panel: '#panel-routes'
        },

        props: ['data'],

        data: function () {
            return this.data;
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
