<template>

    <a title="User"><span class="pf-icon pf-icon-auth" :class="{'pf-parent': data.user}"></span>{{ label }}</a>

    <div class="pf-dropdown" v-show="data.user">

        <table class="pf-table pf-table-dropdown">
            <tbody>
                <tr>
                    <td>Username</td>
                    <td>{{ data.user }}</td>
                </tr>
                <tr>
                    <td>Roles</td>
                    <td>{{ data.roles | json }}</td>
                </tr>
                <tr>
                    <td>Authenticated</td>
                    <td>{{ data.authenticated ? 'yes' : 'no' }}</td>
                </tr>
                <tr>
                    <td>Class</td>
                    <td>{{ data.user_class }}</td>
                </tr>
            </tbody>
        </table>

    </div>

</template>

<script>

    module.exports = {

        section: {
            priority: 60
        },

        props: ['data'],

        replace: false,

        computed: {

            label: function () {

                if (this.data.user) {
                    return this.data.user;
                }

                return this.data.enabled ? 'You are not authenticated.' : 'Authentication is disabled.';
            }

        }

    };

</script>
