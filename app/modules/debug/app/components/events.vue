<template>

    <a title="Events"><span class="pf-icon pf-icon-events"></span> Events</a>

    <script id="panel-events" type="text/template">

        <h1>Events</h1>

        <p v-if="!called && !notcalled">
            <em>No events have been recorded. Are you sure that debugging is enabled in the kernel?</em>
        </p>

        <template v-if="called">
            <h2>Called Listeners</h2>

            <table class="pf-table">
                <tbody>
                <tr>
                    <th>Event name</th>
                    <th>Priority</th>
                    <th>Listener</th>
                </tr>
                <tr v-for="listener in called">
                    <td><code>{{ listener.event }}</code></td>
                    <td>{{ listener.priority }}</td>
                    <td v-if="listener.type === 'Closure'">
                        <a :href="listener.link" v-if="listener.link">{{ listener.relative }}</a>
                        <span v-else>{{ listener.relative }}</span>
                        ({{ listener.line }} - {{ listener.endline }})
                    </td>
                    <td v-if="listener.type === 'Function'">
                        <a :href="listener.link" v-if="listener.link">{{ listener.function }}</a>
                        <span v-else>{{ listener.function }}</span>
                    </td>
                    <td v-if="listener.type === 'Method'">
                        <a :href="listener.link" v-if="listener.link"><abbr :title="listener.class">{{ listener.class | short }}</abbr>::{{ listener.method }}</a>
                        <span v-else><abbr :title="listener.class">{{ listener.class | short }}</abbr>::{{ listener.method }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
        <template v-if="notcalled">

            <h2>Not Called Listeners</h2>

            <table class="pf-table">
                <tbody>
                <tr>
                    <th>Event name</th>
                    <th>Priority</th>
                    <th>Listener</th>
                </tr>
                <tr v-for="listener in notcalled">
                    <td><code>{{ listener.event }}</code></td>
                    <td>{{ listener.priority }}</td>
                    <td v-if="listener.type === 'Closure'">
                        <a :href="listener.link" v-if="listener.link">{{ listener.relative }}</a>
                        <span v-else>{{ listener.relative }}</span>
                        ({{ listener.line }} - {{ listener.endline }})
                    </td>
                    <td v-if="listener.type === 'Function'">
                        <a :href="listener.link" v-if="listener.link">{{ listener.function }}</a>
                        <span v-else>{{ listener.function }}</span>
                    </td>
                    <td v-if="listener.type === 'Method'">
                        <a :href="listener.link" v-if="listener.link"><abbr :title="listener.class">{{ listener.class | short }}</abbr>::{{ listener.method }}</a>
                        <span v-else><abbr :title="listener.class">{{ listener.class | short }}</abbr>::{{ listener.method }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>

    </script>

</template>

<script>

    module.exports = {

        section: {
            priority: 15,
            panel: '#panel-events'
        },

        props: ['data'],

        replace: false,

        filters: {

            short: function (name) {
                return name.split('\\').pop();
            }

        }

    };

</script>
