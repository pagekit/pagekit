<template>

    <a title="Events"><div class="pf-icon pf-icon-events"></div> Events</a>

    <script id="panel-events" type="text/template">

        <h1>Events</h1>

        <table class="pf-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Listener</th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="events">
                    <td>{{ name }}</td>
                    <td><abbr title="{{ listener.details }}">{{ listener.name }}</abbr></td>
                </tr>
            </tbody>
        </table>

    </script>

</template>

<script>

    module.exports = {

        section: {
            priority: 20,
            panel: '#panel-events'
        },

        props: ['data'],

        ready: function () {
            this.$data = this.data;
            this.$parent.add(this);
        }

    };

</script>
