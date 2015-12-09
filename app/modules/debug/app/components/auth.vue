<template>

    <a title="User"><span class="pf-icon pf-icon-auth" :class="{'pf-parent': user}"></span>{{ label }}</a>

    <div class="pf-dropdown" v-show="user">

        <table class="pf-table pf-table-dropdown">
            <tbody>
                <tr>
                    <td>Username</td>
                    <td>{{ user }}</td>
                </tr>
                <tr>
                    <td>Roles</td>
                    <td>{{ roles | json }}</td>
                </tr>
                <tr>
                    <td>Authenticated</td>
                    <td>{{ authenticated ? 'yes' : 'no' }}</td>
                </tr>
                <tr>
                    <td>Class</td>
                    <td>{{ user_class }}</td>
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

        data: function () {
            return this.data;
        },

        computed: {

            label: function () {

                if (this.user) {
                    return this.user;
                }

                return this.enabled ? 'You are not authenticated.' : 'Authentication is disabled.';
            }

        }

    };

</script>
