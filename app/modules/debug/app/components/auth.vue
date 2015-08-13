<template>

    <a title="User"><div class="pf-icon pf-icon-auth" v-class="pf-parent: user"></div> {{ label }}</a>

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

    created: function () {
        this.$data = this.data;
        this.$parent.add(this);
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
